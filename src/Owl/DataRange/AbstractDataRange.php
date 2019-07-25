<?php

declare(strict_types=1);

namespace ApiPlatform\SchemaGenerator\Owl\Datarange;

/**
 * @see https://www.w3.org/TR/2012/REC-owl2-syntax-20121211/#Data_Ranges
 */
abstract class AbstractDataRange
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @return string
     */
    public function iri(): string
    {
        return $this->identifier();
    }

    /**
     * @return string
     */
    public function identifier(): string
    {
        if (null === $this->identifier) {
            $this->makeIdentifier();
        }
        
        if (empty($this->identifier)) {
            throw new \Exception('Undefined identifier');
        }

        return $this->identifier;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $this->makeIdentifier();
        return parent::__toString();
    }
}
