<?php

declare(strict_types=1);

namespace ApiPlatform\SchemaGenerator\Owl\DataRange;

use Psr\Log\LoggerInterface;

/**
 *
 */
class Restriction
{
    public static $types = [
        'xsd:minInclusive',
        'xsd:maxInclusive',
        'xsd:minExclusive',
        'xsd:maxExclusive'
    ];

    private $constrainingFacet;

    private $restrictionValue;

    public function __construct($constrainingFacet, $restrictionValue)
    {
        $this->constrainingFacet = $constrainingFacet;
        $this->restrictionValue = $restrictionValue;
    }
}
