<?php

declare(strict_types=1);

namespace ApiPlatform\SchemaGenerator\DomainGenerator\CodeConfiguration;

class PropertyConfiguration
{
    public const
        VISIBILITY_PUBLIC = 'public',
        VISIBILITY_PROTECTED = 'protected',
        VISIBILITY_PRIVATE = 'private';

    public $uri;

    /** @var ?string */
    public $name;

    /** @var string[] */
    public $implements = [];

    /** @var bool */
    public $static = false;

    /** @var ?Method */
    // Implements X or Y
    // between x and y
    public $validationMethod;

    public $comments = [];

    public function __construct(array $params)
    {
        foreach ($params as $key => $val) {
            $this->$key = $val;
        }
    }
}
