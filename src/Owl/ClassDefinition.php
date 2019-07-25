<?php

declare(strict_types=1);

namespace ApiPlatform\SchemaGenerator\Owl;

use Psr\Log\LoggerInterface;

/**
 * 
 */
class ClassDefinition extends AbstractIriConstruct
{
    /**
     * @var string
     */
    private $label;

    /**
     * @var ?string
     */
    private $comment;

    /**
     * @var
     */
    private $subClassOf;

    /**
     * @var array
     */
    private $properties = [];

    /**
     * @param mixed $iri
     * @param string $label
     * @param string $comment
     * @param mixed $subClassOf
     */
    public function __construct(
        $iri, 
        string $label, 
        ?string $comment = null, 
        $subClassOf = null
    ) {
        parent::__construct($iri);
        $this->label = $label;
        $this->comment = $comment;
        $this->subClassOf = $subClassOf;
    }

    public function addProperty($property)
    {
        $this->properties[] = $property;
    }
}
