<?php

declare(strict_types=1);

namespace ApiPlatform\SchemaGenerator\Owl\DataRange;

use ApiPlatform\SchemaGenerator\Owl\Datatype; 
use Psr\Log\LoggerInterface;

/**
 *
 */
class DatatypeRestriction extends AbstractDataRange
{
    /**
     * @var Datatype
     */
    private $datatype;

    /**
     * [
     *     [constrainingFacet, restrictionValue]
     * ]
     * @var array
     */
    private $restrictions;

    public function __construct(Datatype $datatype, ...$restrictions)
    {
        $this->datatype = $datatype;
        $this->restrictions = $restrictions;
    }

    /**
     *
     */
    protected function makeIdentifier(): void
    {
        $this->identifier = 'generated:restriction#' . md5(
            serialize($this->datatype) .
            serialize($this->restrictions)
        );
    }
}
