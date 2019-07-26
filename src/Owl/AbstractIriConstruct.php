<?php

declare(strict_types=1);

namespace ApiPlatform\SchemaGenerator\Owl;

/**
 * 
 */
class AbstractIriConstruct implements Interfaces\WithIriInterface
{
    /**
     * @var string
     */
    protected $iri;

    /**
     * @param mixed $iri
     */
    public function __construct($iri)
    {
        $this->validateIri($iri);
        $this->iri = $iri;
    }

    /**
     * @return string
     */
    public function iri(): string
    {
        return $this->iri;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->iri;
    }

    /**
     * Throws Exception
     * @param  mixed $iri
     * @return [type]      [description]
     */
    protected function validateIri($iri): void
    {
        if (!is_string($iri)) {
            throw new \Exception('Not a valid IRI');
        }
    }
}
