<?php

declare(strict_types=1);

namespace ApiPlatform\SchemaGenerator\Owl;

use ApiPlatform\SchemaGenerator\Owl;
use ApiPlatform\SchemaGenerator\Owl\Datatype;
use ApiPlatform\SchemaGenerator\Owl\DataRange;
use ApiPlatform\SchemaGenerator\Owl\Registry;
use Psr\Log\LoggerInterface;

/**
 * 
 */
class EasyRdfToOwl
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Owl
     */
    private $owl;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param  \EasyRdf_Graph $graph
     * @return
     */
    public function convertGraph(\EasyRdf_Graph $graph): Owl
    {
        $this->owl = new Owl(new Registry());

        $classes = array_filter(
            $graph->allOfType(NodeType::CLASS_DEFINITION),
            function ($class) { return !$class->isBNode(); }
        );
        $objectProperties = $graph->allOfType(NodeType::OBJECT_PROPERTY);
        $datatypeProperties = $graph->allOfType(NodeType::DATATYPE_PROPERTY);

        foreach ($classes as $class) {
            // $range = $this->registerRange($class);
            $this->registerClass($class);
        }

        foreach ($datatypeProperties as $node) {
            $this->registerDatatypeProperty(
                $node,
                $this->registerRange($node),
                $this->registerDomain($node),
            );
        }

        foreach ($objectProperties as $node) {
            $this->registerObjectProperty(
                $node,
                $this->registerRange($node),
                $this->registerDomain($node),
            );
        }

        return $this->owl;
    }

    //
    // Registry
    //

    /**
     * @param mixed $node
     * @return mixed
     */
    public function registerDomain(\EasyRdf_Resource $node)
    {
        if (!$node->hasProperty(NodeType::DOMAIN)) {
            return;
        }

        $domain = new Domain(
            $this->convertNode($node->get(NodeType::DOMAIN))
        );
        $this->owl->registerDomain($domain);
        return $domain;
    }

    /**
     * @param mixed $node
     * @return mixed
     */
    private function registerRange(\EasyRdf_Resource $node)
    {
        if (!$node->hasProperty(NodeType::RANGE)) {
            return;
        }

        $range = new Range(
            $this->convertNode($node->get(NodeType::RANGE))
        );
        $this->owl->registerRange($range);
        return $range;
    }

    /**
     * @param mixed $node
     */
    private function registerClass(\EasyRdf_Resource $node)
    {
        $class = $this->convertClass($node);
        $this->owl->registerClass($class);
    }

    /**
     * @param mixed $node
     * @param  mixed $range
     */
    private function registerObjectProperty(\EasyRdf_Resource $node, $range, $domain)
    {
        $objectProperty = $this->convertObjectProperty($node)
            ->setRange($range ? $range->iri() : null)
            ->setDomain($domain ? $domain->iri() : null);

        $this->owl->registerObjectProperty($objectProperty);

        // Distribute to class (applicable domain of this property)
        // @todo : distribute to composed range ?
        if ($objectProperty->getDomain()) {
            $domain = $objectProperty->getDomain();
            if ($this->owl->hasClass($domain)) {
                $this->owl->getClass($domain)->addProperty(
                    $objectProperty->iri()
                );
            }
        }
    }

    /**
     * @param mixed $node
     * @param  mixed $range
     */
    private function registerDatatypeProperty(\EasyRdf_Resource $node, $range, $domain)
    {
        $datatypeProperty = $this->convertDatatypeProperty($node)
            ->setRange($range ? $range->iri() : null)
            ->setDomain($domain ? $domain->iri() : null);

        $this->owl->registerDatatypeProperty($datatypeProperty);

        // Distribute to class (applicable domain of this property)
        // @todo : distribute to composed range ?
        if ($datatypeProperty->getDomain()) {
            $domain = $datatypeProperty->getDomain();
            if ($this->owl->hasClass($domain)) {
                $this->owl->getClass($domain)->addProperty(
                    $datatypeProperty->iri()
                );
            }
        }
    }

    //
    // Convert nodes
    //

    /**
     * @param mixed $node
     * @return mixed
     */
    public function convertNode($node)
    {
        $nodeIdentifier = $this->identifyNode($node);
        // var_dump(' => ' . $nodeIdentifier);

        switch ($nodeIdentifier)
        {
            case NodeType::LITERAL:
                return $this->convertLiteral($node);
            case NodeType::COLLECTION:
                return $this->convertCollection($node);
            case NodeType::INTERSECTION_OF:
            case NodeType::COMPLEMENT_OF:
            case NodeType::UNION_OF:
            case NodeType::ONE_OF:
                return $this->convertRange($node, $nodeIdentifier);
            case NodeType::RESTRICTION:
                return $this->convertClassRestriction($node);
            case NodeType::WITH_RESTRICTION:
                return $this->convertRestriction($node);
            // Range constraint
            case NodeType::XSD_MIN_INCLUSIVE:
            case NodeType::XSD_MAX_INCLUSIVE:
            case NodeType::XSD_MIN_EXCLUSIVE:
            case NodeType::XSD_MAX_EXCLUSIVE:
            // SubClass constraint
            case NodeType::MIN_CARDINALITY:
            case NodeType::MAX_CARDINALITY:
            case NodeType::EXACT_CARDINALITY:
                return $this->convertConstraint($node, $nodeIdentifier);
            case NodeType::DATATYPE:
            default:
                return $this->convertDatatype($node);
        }
    }

    /**
     * @param mixed $node
     * @return string
     */
    public function identifyNode($node): string
    {
        if ($node instanceof \EasyRdf_Literal) {
            return NodeType::LITERAL;
        }
        if ($node instanceof \EasyRdf_Collection) {
            return NodeType::COLLECTION;
        }

        $properties = $node->properties();

        if (in_array(NodeType::WITH_RESTRICTION, $properties)) {
            return NodeType::WITH_RESTRICTION;
        }

        if (NodeType::RESTRICTION === $node->type()) {
            return NodeType::RESTRICTION;
        }

        foreach ([
            NodeType::XSD_MIN_INCLUSIVE,
            NodeType::XSD_MAX_INCLUSIVE,
            NodeType::XSD_MIN_EXCLUSIVE,
            NodeType::XSD_MAX_EXCLUSIVE,
        ] as $nodeType) {
            if (in_array($nodeType, $properties)) {
                return $nodeType;
            }
        }

        foreach ([
            NodeType::INTERSECTION_OF,
            NodeType::COMPLEMENT_OF,
            NodeType::UNION_OF,
            NodeType::ONE_OF,
        ] as $nodeType) {
            if (in_array($nodeType, $properties)) {
                return $nodeType;
            }
        }

        return NodeType::DATATYPE;
    }

    //
    // Convert specific node
    //

    private function convertClass($class): ClassDefinition
    {
        return new ClassDefinition(
            $class->getUri(),
            $class->label()
                ? $class->label()->getValue() : '',
            $class->get(NodeType::COMMENT)
                ? $class->get(NodeType::COMMENT)->getValue()
                : null,
            $class->get(NodeType::SUBCLASS_OF)
                ? $this->convertNode($class->get(NodeType::SUBCLASS_OF))
                : null
        );
    }

    private function convertObjectProperty($property): ObjectProperty
    {
        $domain = $property->get(NodeType::DOMAIN)
            ? $this->convertNode($property->get(NodeType::DOMAIN))
            : null;
        // $this->logger->debug($domain->iri());
        if ($domain && $this->owl->registry()->has($domain->iri())) {
            $domain = $domain->iri();
            //var_dump($property->label()->getValue(), $domain);
        }
        return new ObjectProperty(
            $property->getUri(),
            $property->label()
                ? $property->label()->getValue() : '',
            $property->get(NodeType::COMMENT)
                ? $property->get(NodeType::COMMENT)->getValue()
                : null,
            $property->get(NodeType::SUBPROPERTY_OF)
                ? $this->convertNode($property->get(NodeType::SUBPROPERTY_OF))
                : null,
            $domain,
        );
    }

    private function convertDatatypeProperty($property): DatatypeProperty
    {
        $domain = $property->get(NodeType::DOMAIN)
            ? $this->convertNode($property->get(NodeType::DOMAIN))
            : null;
        if ($domain && $this->owl->registry()->has($domain->iri())) {
            $domain = $domain->iri();
            //var_dump($property->label()->getValue(), $domain);
        }
        return new DatatypeProperty(
            $property->getUri(),
            $property->label()
                ? $property->label()->getValue() : '',
            $property->get(NodeType::COMMENT)
                ? $property->get(NodeType::COMMENT)->getValue()
                : null,
            $property->get(NodeType::SUBPROPERTY_OF)
                ? $this->convertNode($property->get(NodeType::SUBPROPERTY_OF))
                : null,
            $domain,
        );
    }

    //
    //
    //

    private function convertCollection(\EasyRdf_Collection $collection): array
    {
        $parts = [];
        while ($current = $collection->current()) {
            $parts[] = $this->convertNode($current);
            $collection->next();
        }
        $collection->rewind();
        return $parts;
    }

    private function convertLiteral(\EasyRdf_Literal $literal): Literal
    {
        return new Literal($literal->getValue());
    }

    private function convertDatatype($datatype): Datatype
    {
        return new Datatype($datatype->getUri());
    }

    private function convertRange($range, $nodeIri)
    {
        switch ($nodeIri) {
            case NodeType::COMPLEMENT_OF:
                $class = DataRange\DataComplementOf::class;
                break;
            case NodeType::INTERSECTION_OF:
                $class = DataRange\DataIntersectionOf::class;
                break;
            case NodeType::ONE_OF:
                $class = DataRange\DataOneOf::class;
                break;
            case NodeType::UNION_OF:
                $class = DataRange\DataUnionOf::class;
                break;
        }        

        $params = $this->convertNode($range->get($nodeIri));
        if (!($params instanceof \Traversable) && !is_array($params)) {
            $params = [$params];
        }

        return new $class(...$params); 
    }

    private function convertRestriction($node): DataRange\DatatypeRestriction
    {
        $params = $this->convertNode($node->get(NodeType::WITH_RESTRICTION));
        if (!($params instanceof \Traversable) && !is_array($params)) {
            $params = [$params];
        }

        return new DataRange\DatatypeRestriction(
            $this->convertDatatype($node->get(NodeType::RESTRICTION_DATATYPE)),
            ...$params
        );
    }

    private function convertConstraint($node, $nodeIdentifier): DataRange\Restriction
    {
        return new DataRange\Restriction(
            $nodeIdentifier,
            $node->get($nodeIdentifier)->getValue()
        );
    }

    private function convertClassRestriction($node): DataRange\DatatypeRestriction
    {
        $restrictions = $this->convertNode($node->get(NodeType::ALL_VALUES_FROM));
        if (!($restrictions instanceof \Traversable) && !is_array($restrictions)) {
            $restrictions = [$restrictions];
        }

        return new DataRange\DatatypeRestriction(
            $this->convertDatatype($node->get(NodeType::ON_PROPERTY)),
            ...$restrictions
        );
    }
}
