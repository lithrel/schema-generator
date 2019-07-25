<?php

declare(strict_types=1);

namespace ApiPlatform\SchemaGenerator;

use ApiPlatform\SchemaGenerator\AnnotationGenerator\AnnotationGeneratorInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Inflector\Inflector;
use MyCLabs\Enum\Enum;
use PhpCsFixer\Cache\NullCacheManager;
use PhpCsFixer\Differ\NullDiffer;
use PhpCsFixer\Error\ErrorsManager;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\Linter\Linter;
use PhpCsFixer\RuleSet;
use PhpCsFixer\Runner\Runner;
use Psr\Log\LoggerInterface;
use ApiPlatform\SchemaGenerator\Generator\GeneratableClass;

/**
 * Abstract annotation generator.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
abstract class AbstractGenerator implements TypesGeneratorInterface
{

    /**
     * @var string
     *
     * @internal
     */
    // isEnum
    public const SCHEMA_ORG_ENUMERATION = 'http://schema.org/Enumeration';

    /**
     * @var string
     */
    protected const SCHEMA_ORG_DOMAIN = 'schema:domainIncludes';

    /**
     * @var string
     */
    protected const SCHEMA_ORG_RANGE = 'schema:rangeIncludes';

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var \EasyRdf_Graph[]
     */
    protected $graphs;

    /**
     * @var CardinalitiesExtractor
     */
    protected $cardinalitiesExtractor;

    /**
     * @var GoodRelationsBridge
     */
    protected $goodRelationsBridge;

    /**
     * @var array
     */
    protected $cardinalities;

    /**
     * @var array
     */
    protected $resourceTypes = [
        'class' => 'rdfs:Class',
        'comment' => 'rdfs:comment',
        'subClass' => 'rdfs:subClassOf',
        'property' => 'rdf:property',
    ];

    /**
     * @param \EasyRdf_Graph[] $graphs
     */
    public function __construct(\Twig_Environment $twig, LoggerInterface $logger, array $graphs, CardinalitiesExtractor $cardinalitiesExtractor, GoodRelationsBridge $goodRelationsBridge)
    {
        if (!$graphs) {
            throw new \InvalidArgumentException('At least one graph must be injected.');
        }

        $this->twig = $twig;
        $this->logger = $logger;
        $this->graphs = $graphs;
        $this->cardinalitiesExtractor = $cardinalitiesExtractor;
        $this->goodRelationsBridge = $goodRelationsBridge;

        $this->cardinalities = $this->cardinalitiesExtractor->extract();
    }

    public function getBaseClass()
    {
        return new GeneratableClass();
    }

    public function defineNamespace($class, array $config)
    {
        if ($class->isEnum()) {
            $class->setNamespace($class->config['namespace'] ?? $config['namespaces']['enum']);
            $class->setParent('Enum');
            return;
        }

        $class->setNamespace($class->config['namespaces']['class'] ?? $config['namespaces']['entity']);
        $class->setParent($class->config['parent'] ?? null);
        if (empty($class->getParent())) {
            $type = $class->getResource();
            $subClasses = $type->all($this->resourceTypes['subClass']);
            $numberOfSupertypes = count($subClasses);
            if ($numberOfSupertypes > 1) {
                $this->logger->warning(sprintf('The type "%s" has several supertypes. Using the first one.', $type->localName()));
            }
            $class->setParent($numberOfSupertypes ? subClasses[0]->localName() : '');
        }
    }

    public function defineUses($class, array $config)
    {
        if ($class->isEnum()) {
            $class->setUse(Enum::class);
            return;
        }

        $parent = $class->getParent();
        if (!empty($parent) && isset($config['types'][$parent]['namespaces']['class'])) {
            $parentNamespace = $config['types'][$parent]['namespaces']['class'];

            if ($parentNamespace !== $class->getNamespace()) {
                $class->setUses($parentNamespace.'\\'.$class['parent']);
            }
        }
    }

    public function defineEmbeddable($class)
    {
        $class->setIsEmbeddable($class->config['embeddable'] ?? false);
    }

    public function defineInterface($class, array $config)
    {
        if ($config['useInterface']) {
            $class['interfaceNamespace'] = !empty($class->config['namespaces']['interface'])
                ? $class->config['namespaces']['interface']
                : $config['namespaces']['interface'];
            $class['interfaceName'] = sprintf('%sInterface', $class->getName());
        }
    }

    public function defineAbstract($class, $config)
    {
        $class->setAbstract($config['types'][$class->getName()]['abstract'] ?? $class->hasChild());
    }

    /**
     * @param array $config
     */
    public function generate(array $config): void
    {
        $typesToGenerate = $this->getTypesToGenerate($config);
        $propertiesMap = $this->createPropertiesMap($typesToGenerate);
        

        $classes = [];
        foreach ($typesToGenerate as $typeName => $type) {
            $typeName = is_string($typeName) ? $typeName : $type->localName();
            $typeConfig = $config['types'][$typeName] ?? null;
            $comment = $type->get($this->resourceTypes['comment']);

            $class = $this->getBaseClass();
            $class->setName($typeName);
            $class->setLabel($comment ? $comment->getValue() : '');
            $class->setResource($type);
            $class->setConfig($typeConfig);
            $class->isEnum($this->isEnum($type));

            $this->defineNamespace($class, $config);
            $this->defineUses($class, $config);
            $this->defineEmbeddable($class);
            $this->defineInterface($class, $config);
            
            $this->configureProperties($class, $type, $typeName, $typeConfig, $propertiesMap, $config);

            $classes[$typeName] = $class;
        }

        // Verify parent existence
        // @todo : can be done in a classes container
        foreach ($classes as $class) {
            $parent = $class->getParent();
            if (!empty($parent) && isset($classes[$parent])) {
                $classes[$parent]->setHasChild(true);
                $class->setParentHasConstructor($classes[$parent]->hasConstructor());
            }
            // @todo : if parent doesn't exists, generate it
        }

        // Define abstract
        // Depends on child presence - to be done after parent
        foreach ($classes as $class) {
            $this->defineAbstract($class, $config);
        }

        // Properties precisions
        foreach ($classes as $class) {
            $properties = $class->getProperties();
            foreach ($class->getProperties() as &$property) {
                $property['isEnum'] = isset($classes[$property['range']]) && $classes[$property['range']]['isEnum'];
                $property['typeHint'] = $this->fieldToTypeHint($config, $property, $classes) ?? false;

                if ($property['isArray']) {
                    $property['adderRemoverTypeHint'] = $this->fieldToAdderRemoverTypeHint($property, $classes) ?? false;
                }
            }
            $class->setProperties($properties);
        }

        // Ignore properties included in parent
        foreach ($classes as &$class) {
            // When including all properties, ignore properties already set on parent
            if (isset($class->getConfig()['allProperties']) 
                && $class->getConfig()['allProperties'] 
                && isset($classes[$class->getParent()])
            ) {
                $this->filterPropertiesFromParents($class, $classes, $propertiesMap, $config);
            }
        }

        // Id generator
        if ($config['id']['generate']) {
            $this->configureIdGenerator($classes, $config);
        }

        $this->generateAnnotations($classes, $typesToGenerate, $config);

        $this->render($classes, $config);
    }

    public function filterPropertiesFromParents($class, $classes, $propertiesMap, $config)
    {
        $type = $class->getResource();

        foreach ($propertiesMap[$type->getUri()] as $property) {
            if (!$class->propertyExists($property->localName())) {
                continue;
            }

            $parentConfig = $config['types'][$class['parent']] ?? null;
            $parentClass = $classes[$class['parent']];

            while ($parentClass) {
                if (!isset($parentConfig['properties']) || !is_array($parentConfig['properties'])) {
                    // Unset implicit property
                    $parentType = $parentClass['resource'];
                    if (in_array($property, $propertiesMap[$parentType->getUri()], true)) {
                        $class->unsetProperty($property->localName());
                        continue 2;
                    }
                } else {
                    // Unset explicit property
                    if (array_key_exists($property->localName(), $parentConfig['properties'])) {
                        $class->unsetProperty($property->localName());
                        continue 2;
                    }
                }

                $parentConfig = $parentClass['parent'] ? ($config['types'][$parentClass['parent']] ?? null) : null;
                $parentClass = $parentClass['parent'] ? $classes[$parentClass['parent']] : null;
            }
        }
    }
}