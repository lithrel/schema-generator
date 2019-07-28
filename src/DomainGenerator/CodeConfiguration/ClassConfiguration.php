<?php

declare(strict_types=1);

namespace ApiPlatform\SchemaGenerator\DomainGenerator\CodeConfiguration;

class ClassConfiguration
{
    public const
        TYPE_CLASS = 'class',
        TYPE_INTERFACE = 'interface',
        TYPE_TRAIT = 'trait';

    public const
        VISIBILITY_PUBLIC = 'public',
        VISIBILITY_PROTECTED = 'protected',
        VISIBILITY_PRIVATE = 'private';

    /** @var ?string */
    public $uri;

    /** @var ?string */
    public $namespace;

    /** @var ?string */
    public $name;

    /** @var string  class|interface|trait */
    public $type = self::TYPE_CLASS;

    /** @var bool */
    public $final = false;

    /** @var bool */
    public $abstract = false;

    /** @var string[] */
    public $extends = [];
    
    /** @var string[] */
    public $implements = [];
    
    /** @var array[] */
    public $traits = [];
    
    /** @var string[] name => Constant */
    public $consts = [];
    
    /** @var string[] name => Property */
    public $properties = [];
    
    /** @var string[] name => Method */
    public $methods = [];

    public function __construct(array $params)
    {
        foreach ($params as $key => $val) {
            $this->$key = $val;
        }
    }
}
