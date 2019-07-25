<?php

declare(strict_types=1);

namespace ApiPlatform\SchemaGenerator\DomainGenerator\Interfaces;

interface ValidablePropertyInterface
{
    public function validate(): bool;
}
