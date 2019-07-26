<?php

declare(strict_types=1);

namespace ApiPlatform\SchemaGenerator\Owl;


use Psr\Log\LoggerInterface;

/**
 * @method ClassDefinition[] classes()
 * @method DatatyeProperty[] datatypeProperties()
 * @method Domain[] domains()
 * @method ObjectProperty[] objectProperties()
 * @method Range[] ranges()
 */
class Registry implements Interfaces\RegistryInterface
{
    /**
     * @var array
     */
    private $categories = [
        ClassDefinition::class => 'classes',
        DatatypeProperty::class => 'datatypeProperties',
        ObjectProperty::class => 'objectProperties',
        Domain::class => 'domains',
        Range::class => 'ranges',
    ];

    /**
     * @var ClassDefinition[]
     */
    private $classes = [];

    /**
     * @var DatatypeProperty[]
     */
    private $datatypeProperties = [];

    /**
     * @var Domain[]
     */
    private $domains = [];

    /**
     * @var ObjectProperty[]
     */
    private $objectProperties = [];

    /**
     * @var Range[]
     */
    private $ranges = [];

    /**
     * @param mixed $object
     * @throws \Exception
     */
    public function add(Interfaces\WithIriInterface $object): void
    {
        $this->validateObject($object);
        $category = $this->categories[get_class($object)];
        if (!isset($this->$category[$object->iri()])) {
            $this->$category[$object->iri()] = $object;
        }
    }

    /**
     * @param string $iri
     * @return ?object
     */
    public function get(string $iri): ?object
    {
        foreach ($this->categories as $category) {
            if (isset($this->$category[$iri])) {
                return $this->$category[$iri];
            }
        }

        return null;
    }

    /**
     * @param string $iri
     * @return bool
     */
    public function has(string $iri): bool
    {
        foreach ($this->categories as $category) {
            if (isset($this->$category[$iri])) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return ClassDefinition[]
     */
    public function classes(): array
    {
        return $this->classes;
    }

    /**
     * @return DatatypeProperty[]
     */
    public function datatypeProperties(): array
    {
        return $this->datatypeProperties;
    }

    /**
     * @return Domain[]
     */
    public function domains(): array
    {
        return $this->domains;
    }

    /**
     * @return ObjectProperty[]
     */
    public function objectProperties(): array
    {
        return $this->objectProperties;
    }

    /**
     * @return Range[]
     */
    public function ranges(): array
    {
        return $this->ranges;
    }

    //

    /**
     * @param WithIriInterface $object
     * @throws \Exception
     */
    private function validateObject(Interfaces\WithIriInterface $object)
    {
        $class = get_class($object);
        if (!isset($this->categories[$class])) {
            throw new \Exception('Class not supported in registry: ' . $class);
        }
    }

    /**
     * @param string $category
     * @throws \Exception
     */
    private function validateCategory(string $category)
    {
        if (!in_array($category, $this->categories)) {
            throw new \Exception('Category unknown in registry: ' . $category);
        }
    }
}
