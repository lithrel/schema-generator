<?php

declare(strict_types=1);

namespace ApiPlatform\SchemaGenerator\Owl;
 
use Psr\Log\LoggerInterface;

/**
 *
 */
class Range extends AbstractIriConstruct
{
    private $definition;

    public function __construct($definition)
    {
        $this->definition = $definition;
        $this->iri = 'range:' . $this->definition->iri();
    }
}
