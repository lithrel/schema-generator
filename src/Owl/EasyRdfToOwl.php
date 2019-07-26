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
class EasyRdfToOwl implements Interfaces\ToOwlConverterInterface
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
     * @param  \EasyRdf_Graph $origin
     * @return
     */
    public function convert($origin, Owl $owl): Owl
    {
        if (!($origin instanceof \EasyRdf_Graph)) {
            throw new \Exception('Unable to convert ' . get_class($origin));
        }
        $this->owl = $owl;

        $classes = array_filter(
            $origin->allOfType(NodeType::CLASS_DEFINITION),
            function ($class) { return !$class->isBNode(); }
        );
        $objectProperties = $origin->allOfType(NodeType::OBJECT_PROPERTY);
        $datatypeProperties = $origin->allOfType(NodeType::DATATYPE_PROPERTY);

        foreach ($classes as $class) {
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
     */
    private function registerClass(\EasyRdf_Resource $node)
    {
        $class = $this->convertClass($node);
        $this->owl->register($class);
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

        $range = $this->convertRange($node->get(NodeType::RANGE));
        $this->owl->register($range);
        return $range;
    }

    /**
     * @param mixed $node
     * @return mixed
     */
    private function registerDomain(\EasyRdf_Resource $node)
    {
        if (!$node->hasProperty(NodeType::DOMAIN)) {
            return;
        }

        $domain = $this->convertDomain($node->get(NodeType::DOMAIN));
        $this->owl->register($domain);
        return $domain;
    }

    /**
     * @param mixed $node
     * @param  mixed $range
     */
    private function registerDatatypeProperty(\EasyRdf_Resource $node, ?Range $range, ?Domain $domain)
    {
        $datatypeProperty = $this->convertDatatypeProperty($node)
            ->setRange($range ? $range->iri() : null)
            ->setDomain($domain ? $domain->iri() : null);

        $this->owl->register($datatypeProperty);
        $this->distributeProperty($datatypeProperty);
    }

    /**
     * @param mixed $node
     * @param  mixed $range
     */
    private function registerObjectProperty(\EasyRdf_Resource $node, ?Range $range, ?Domain $domain)
    {
        $objectProperty = $this->convertObjectProperty($node)
            ->setRange($range ? $range->iri() : null)
            ->setDomain($domain ? $domain->iri() : null);

        $this->owl->register($objectProperty);
        $this->distributeProperty($objectProperty);
    }

    /**
     * Distribute property to class
     * (applicable domain of this property)
     * @param ObjectProperty|DatatypeProperty $property
     */
    private function distributeProperty($property): void
    {
        if (!$property->getDomain()) {
            return;
        }

        // substr 'domain:' IRI prefix
        $domain = substr($property->getDomain(), 7);
        if ($this->owl->hasClass($domain)) {
            $this->owl->getClass($domain)->addProperty(
                $property->iri()
            );
        }
    }

    //
    // Convert nodes
    //

    /**
     * @param mixed $node
     * @return string
     */
    private function identifyNode($node): string
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

    /**
     * @param mixed $node
     * @return mixed
     */
    private function convertNode($node)
    {
        $nodeIdentifier = $this->identifyNode($node);

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
                return $this->convertDataRange($node, $nodeIdentifier);
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

    //
    // Convert main nodes
    //

    private function convertClass(\EasyRdf_Resource $class): ClassDefinition
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

    private function convertObjectProperty(\EasyRdf_Resource $property): ObjectProperty
    {
        return new ObjectProperty(
            $property->getUri(),
            $property->label() ? $property->label()->getValue() : '',
            $property->hasProperty(NodeType::COMMENT)
                ? $property->get(NodeType::COMMENT)->getValue()
                : null,
            $property->hasProperty(NodeType::SUBPROPERTY_OF)
                ? $this->convertNode($property->get(NodeType::SUBPROPERTY_OF))
                : null,
            $this->retrieveDomain($property),
        );
    }

    private function convertDatatypeProperty(\EasyRdf_Resource $property): DatatypeProperty
    {
        return new DatatypeProperty(
            $property->getUri(),
            $property->label() ? $property->label()->getValue() : '',
            $property->get(NodeType::COMMENT)
                ? $property->get(NodeType::COMMENT)->getValue()
                : null,
            $property->get(NodeType::SUBPROPERTY_OF)
                ? $this->convertNode($property->get(NodeType::SUBPROPERTY_OF))
                : null,
            $this->retrieveDomain($property),
        );
    }

    private function convertRange(\EasyRdf_Resource $range): Range
    {
        return new Range($this->convertNode($range));
    }

    private function convertDomain(\EasyRdf_Resource $domain): Domain
    {
        return new Domain($this->convertNode($domain));
    }

    /**
     * Replace domain by domain IRI if known
     * @param \EasyRdf_Resource $node
     * @return mixed
     */
    private function retrieveDomain(\EasyRdf_Resource $node)
    {
        if (!$node->hasProperty(NodeType::DOMAIN)) {
            return null;
        }

        $domain = $this->convertDomain($node->get(NodeType::DOMAIN));
        if ($this->owl->registry()->has($domain->iri())) {
            $domain = $domain->iri();
        }
        return $domain;
    }

    //
    // Converter specific cases
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

    private function convertDataRange(\EasyRdf_Resource $range, string $nodeIri)
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

    private function convertRestriction(\EasyRdf_Resource $node): DataRange\DatatypeRestriction
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

    private function convertConstraint(\EasyRdf_Resource $node, string $nodeIdentifier): DataRange\Restriction
    {
        return new DataRange\Restriction(
            $nodeIdentifier,
            $node->get($nodeIdentifier)->getValue()
        );
    }

    private function convertClassRestriction(\EasyRdf_Resource $node): DataRange\DatatypeRestriction
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
