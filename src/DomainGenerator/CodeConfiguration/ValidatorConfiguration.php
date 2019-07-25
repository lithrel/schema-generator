<?php

declare(strict_types=1);

namespace ApiPlatform\SchemaGenerator\DomainGenerator\CodeConfiguration;

class ValidatorConfiguration
{
    const CONSTRAINT_UNION = 'union';
    const CONSTRAINT_DATATYPE = 'type';
    const CONSTRAINT_ENUM = 'enum';


    private $types;

    private $enums;

    private $restrictions;


}
