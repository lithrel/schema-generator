<?php

declare(strict_types=1);

namespace ApiPlatform\SchemaGenerator\Owl\Interfaces;

/**
 *
 */
interface WithIriInterface
{
    /**
     * @return string
     */
    public function iri(): string;
}