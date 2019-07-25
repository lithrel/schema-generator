<?php

declare(strict_types=1);

namespace ApiPlatform\SchemaGenerator\Owl;

/**
 * @see https://www.w3.org/TR/2012/REC-owl2-syntax-20121211/#Data_Ranges
 */
class Datatype extends AbstractIriConstruct
{
    public function identifier()
    {
        return $this->__toString();
    }
}
