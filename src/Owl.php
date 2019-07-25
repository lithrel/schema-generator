<?php

declare(strict_types=1);

namespace ApiPlatform\SchemaGenerator;


use ApiPlatform\SchemaGenerator\Owl\ClassDefinition; 
use ApiPlatform\SchemaGenerator\Owl\Datatype;
use ApiPlatform\SchemaGenerator\Owl\Registry;
use Psr\Log\LoggerInterface;

/**
 *
 */
class Owl
{
    private $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public function registry()
    {
        return $this->registry;
    }

    public function hasClass(string $iri): bool
    {
        return isset($this->registry()->classes[$iri]);
    }

    public function getClass(string $iri): ClassDefinition
    {
        return $this->registry()->classes[$iri];
    }

    //
    // Register
    //

    public function registerClass(ClassDefinition $class)
    {
        $this->registry()->addClass($class);
    }

    public function registerObjectProperty($objectProperty)
    {
        $this->registry()->addObjectProperty($objectProperty);
    }

    public function registerDatatypeProperty($datatypeProperty)
    {
        $this->registry()->addDatatypeProperty($datatypeProperty);
    }

    public function registerRange($range)
    {
        $this->registry()->addRange($range);
    }

    public function registerDomain($domain)
    {
        $this->registry()->addDomain($domain);
    }
}
