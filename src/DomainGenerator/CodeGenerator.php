<?php

declare(strict_types=1);

namespace ApiPlatform\SchemaGenerator\DomainGenerator;

use Psr\Log\LoggerInterface;
use ApiPlatform\SchemaGenerator\DomainGenerator\CodeConfiguration\ClassConfiguration;
use ApiPlatform\SchemaGenerator\DomainGenerator\CodeConfiguration\PropertyConfiguration;
use ApiPlatform\SchemaGenerator\Owl;
use ApiPlatform\SchemaGenerator\Owl\Registry;
use ApiPlatform\SchemaGenerator\Owl\DataRange;
use ApiPlatform\SchemaGenerator\Owl\EasyRdfToOwl;

class CodeGenerator
{
    public const ROOT_OBJECT = '__ROOT__';

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ClassGeneratorInterface
     */
    private $classGenerator;

    /**
     * @var \EasyRdf_Graph
     */
    private $graphs;

    /**
     * @var array
     */
    private $config;

    /**
     * @var array
     */
    private $localMap = [];
    private $heritanceMap = []; // parent -> children
    private $parentsByChild = []; // child -> parents

    private $propertyHeritanceMap = [];

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(
        LoggerInterface $logger,
        ClassGenerator $classGenerator
    ) {
        $this->logger = $logger;
        $this->classGenerator = $classGenerator;
    }

    public function configure(array $config): self
    {
        $this->config = $config;
        return $this;
    }

    public function loadGraphs($graphs): self
    {
        $this->graphs = $graphs;
        return $this;
    }

    /**
     * @todo : make Collection with ArrayAccess/Iterator
     */
    public function generate(): void
    {
        $owl = (new EasyRdfToOwl($this->logger))
            ->convert($this->graphs[0], new Owl(new Registry()));

        $namespace = 'lithrel\\ActivityStreams';
        $registry = $owl->registry();
        $classes = $registry->classes();

        $generator = new ClassGenerator();
        foreach ($classes as $class) {
            $parent = $class->parent();
            $hasParent = $parent
                && method_exists($parent, 'iri')
                && $registry->has($parent->iri());

            $conf = new ClassConfiguration([
                'namespace' => $namespace,
                'name' => $class->label(),
                'extends' => $hasParent
                    ? [$namespace . '\\' . $registry->get($parent->iri())->label()]
                    : [],
                'properties' => array_map(
                    function ($prop) use ($registry) {
                        $def = $registry->get($prop);
                        return new PropertyConfiguration([
                            'name' => $def->label(),
                            'comments' => $def->comment(),
                        ]);
                    },
                    $class->properties()
                ),
            ]);

            $generator->generate($conf);
        }
    }
}
