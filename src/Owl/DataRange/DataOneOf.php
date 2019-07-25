<?php

declare(strict_types=1);

namespace ApiPlatform\SchemaGenerator\Owl\DataRange;

use Psr\Log\LoggerInterface;

/**
 *
 */
class DataOneOf extends AbstractDataRange
{
    /**
     * @var array
     */
    private $enums = [];

    /**
     *
     */
    public function __construct(...$enums)
    {
        $this->enums = $enums;
    }

    /**
     *
     */
    protected function makeIdentifier(): void
    {
        sort($this->enums);
        $this->identifier = 'generated:oneOf#' .
            md5(serialize($this->enums));
    }
}
