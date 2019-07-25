<?php

declare(strict_types=1);

namespace ApiPlatform\SchemaGenerator\Owl\DataRange;

use Psr\Log\LoggerInterface;

/**
 * 
 */
class DataComplementOf extends AbstractDataRange
{
    /**
     * @var DataRange[]
     */
    private $sets = [];

    /**
     *
     */
    public function __construct(...$sets)
    {
        $this->sets = $sets;
    }

    /**
     *
     */
    protected function makeIdentifier(): void
    {
        sort($this->sets);
        $this->identifier = 'generated:complementOf#' .
            md5(serialize($this->sets));
    }
}
