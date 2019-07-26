<?php

declare(strict_types=1);

namespace ApiPlatform\SchemaGenerator\Owl;
 
use Psr\Log\LoggerInterface;

/**
 *
 */
class Domain extends AbstractIriConstruct
{
    /**
     * @var mixed
     */
    private $definition;

    /**
     * @param mixed $definition
     */
    public function __construct($definition)
    {
        $this->definition = $definition;
        $this->iri = 'domain:' . $this->definition->iri();
    }
}
