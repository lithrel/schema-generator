<?php

declare(strict_types=1);

namespace ApiPlatform\SchemaGenerator;


use ApiPlatform\SchemaGenerator\Owl\ClassDefinition; 
use ApiPlatform\SchemaGenerator\Owl\Datatype;
use ApiPlatform\SchemaGenerator\Owl\Interfaces\RegistryInterface;
use ApiPlatform\SchemaGenerator\Owl\Interfaces\WithIriInterface;

/**
 *
 */
class Owl
{
    /**
     * @var RegistryInterface
     */
    private $registry;

    /**
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @return RegistryInterface
     */
    public function registry(): RegistryInterface
    {
        return $this->registry;
    }

    /**
     * @param string $iri
     * @return boolean
     */
    public function hasClass(string $iri): bool
    {
        return isset($this->registry()->classes()[$iri]);
    }

    /**
     * @param string $iri
     * @return ?ClassDefinition
     */
    public function getClass(string $iri): ?ClassDefinition
    {
        return $this->registry()->classes()[$iri];
    }

    /**
     * @param WithIriInterface $object
     */
    public function register(WithIriInterface $object): void
    {
        $this->registry()->add($object);
    }
}
