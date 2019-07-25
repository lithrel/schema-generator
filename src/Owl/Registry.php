<?php

declare(strict_types=1);

namespace ApiPlatform\SchemaGenerator\Owl;


use Psr\Log\LoggerInterface;

/**
 *
 */
class Registry
{
    private $classes = [];

    private $datatypeProperties = [];

    private $domains = [];

    private $objectProperties = [];

    private $ranges = [];

    private $categories = [
        'classes' => ClassDefinition::class,
        'datatypeProperties' => DatatypeProperty::class,
        'objectProperties' => ObjectProperty::class,
        'domains' => Domain::class,
        'ranges' => Range::class,
    ];

    public function __construct()
    {
    }

    public function addClass(ClassDefinition $class)
    {
        $this->classes[$class->iri()] = $class;
    }

    public function addDatatypeProperty(DatatypeProperty $property)
    {
        $this->datatypeProperties[$property->iri()] = $property;
    }

    public function addObjectProperty(ObjectProperty $property)
    {
        $this->objectProperties[$property->iri()] = $property;
    }

    public function addDomain($domain)
    {
        $this->domains[$domain->iri()] = $domain;
    }

    public function addRange($range)
    {
        $this->ranges[$range->iri()] = $range;
    }

    //

    public function __call(string $name, array $arguments)
    {
        // Read
        $category = lcfirst($name);
        $this->validateCategory($category);
        return $this->$category;
    }

    public function get(string $iri)
    {
        foreach (array_flip($this->categories) as $category) {
            if (isset($this->$cat[$iri])) {
                return $this->$cat[$iri];
            }
        }
        return null;
    }

    public function has(string $iri): bool
    {
        foreach (array_flip($this->categories) as $category) {
            if (isset($this->$category[$iri])) {
                return true;
            }
        }

        return false;
    }

    private function categories(): \ArrayIterator
    {
        return $this->categories;
    }

    private function validateCategory($category)
    {
        if (!isset($this->categories[$category])) {
            throw new \Exception('Category unknown in registry: ' . $name);
        }
    }
}