<?php

declare(strict_types=1);

namespace ApiPlatform\SchemaGenerator\Owl;


use ApiPlatform\SchemaGenerator\Owl\ClassDefinition; 
use ApiPlatform\SchemaGenerator\Owl\Datatype; 
use Psr\Log\LoggerInterface;

/**
 *
 */
class ObjectProperty extends AbstractIriConstruct
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
     * @var [type]
     */
    private $domain;

    /**
     * @var
     */
    private $subPropertyOf;

    /**
     * 
     */
    public function __construct(
        string $iri,
        string $label, 
        ?string $comment = null, 
        $subPropertyOf = null,
        $domain = null,
        $range = null
    ) {
        parent::__construct($iri);
        $this->label = $label;
        $this->comment = $comment;
        $this->subPropertyOf = $subPropertyOf;

        $this->domain = $domain;
        $this->range = $range;
    }

    public function label(): string
    {
        return $this->label;
    }

    public function comment(): ?string
    {
        return $this->comment;
    }

    public function setRange($range): self
    {
        $this->range = $range;
        return $this;
    }

    public function setDomain($domain): self
    {
        $this->domain = $domain;
        return $this;
    }

    public function getDomain()
    {
        return $this->domain;
    }
}