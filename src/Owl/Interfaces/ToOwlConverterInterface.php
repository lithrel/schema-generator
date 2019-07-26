<?php

declare(strict_types=1);

namespace ApiPlatform\SchemaGenerator\Owl\Interfaces;

use ApiPlatform\SchemaGenerator\Owl;

/**
 *
 */
interface ToOwlConverterInterface
{
    /**
     * @param mixed $origin
     * @param Owl $owl
     * @return Owl
     */
    public function convert($origin, Owl $owl): Owl;
}
