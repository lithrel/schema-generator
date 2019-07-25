<?php

declare(strict_types=1);

namespace ApiPlatform\SchemaGenerator\DomainGenerator;

use Psr\Log\LoggerInterface;
use ApiPlatform\SchemaGenerator\DomainGenerator\CodeConfiguration\ClassConfiguration;
use ApiPlatform\SchemaGenerator\DomainGenerator\CodeConfiguration\PropertyConfiguration;
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
     * 
     */
    public function generate(): void
    {
        $owl = (new EasyRdfToOwl($this->logger))
            ->convertGraph($this->graphs[0]);

        var_dump(array_keys($owl->registry()->domains()));
        var_dump(array_keys($owl->registry()->ranges()));
        var_dump(array_keys($owl->registry()->classes()));
    }
}
