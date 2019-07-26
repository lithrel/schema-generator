<?php

declare(strict_types=1);

namespace ApiPlatform\SchemaGenerator\Owl\Interfaces;

/**
 *
 */
interface RegistryInterface
{
    /**
     * @param mixed $object
     */
    public function add(WithIriInterface $object): void;

    /**
     * @param string $iri
     * @return ?object
     */
    public function get(string $iri): ?object;

    /**
     * @param string $iri
     * @return bool
     */
    public function has(string $iri): bool;

    //

    /**
     * @return ClassDefinition[]
     */
    public function classes(): array;

    /**
     * @return DatatypeProperty[]
     */
    public function datatypeProperties(): array;

    /**
     * @return Domain[]
     */
    public function domains(): array;

    /**
     * @return ObjectProperty[]
     */
    public function objectProperties(): array;

    /**
     * @return Range[]
     */
    public function ranges(): array;
}
