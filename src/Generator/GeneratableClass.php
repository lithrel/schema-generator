<?php

/*
 * This file is part of the API Platform project.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace ApiPlatform\SchemaGenerator\Generator;

class GeneratableClass
{
    private $name;

    private $label;

    private $uses = [];

    /**
     * ['name', 'visibility']
     */
    private $constants = [];

    // private $fields;
    /**
     * ['name', 'visibility', 'typehint', 'default']
     */
    private $properties = [];

    private $methods;

    private $resource;

    private $config;

    private $parent;

    private $hasConstructor = false;

    private $parentHasConstructor = false;

    private $hasChild = false;

    private $abstract = false;

    private $isEnum = false;

    public function setNamespace(string $namespace)
    {
        $this->namespace = $namespace;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function setLabel(string $label)
    {
        $this->label = $label;
    }

    public function setIsEnum(bool $isEnum)
    {
        $this->isEnum = $isEnum;
    }

    public function isEnum(): bool
    {
        return $this->isEnum;
    }

    public function setUse(string $qualifiedName, ?string $alias = null)
    {
        $this->uses[$qualifiedName] = [
            'name' => $qualifiedName,
            'alias' => $alias
        ];
    }

    public function setProperty(string $name, string $visibility, $default = null)
    {
        $this->properties[$name] = [
            'visiblity' => $visibility,
            'default' => $default
        ];
    }

    public function propertyExists(string $name): bool
    {
        return isset($this->properties[$name]);
    }

    public function unsetProperty($name)
    {
        unset($this->properties[$name]);
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getResource(): \EasyRdf_Resource
    {
        return $this->resource;
    }
}