<?php

declare(strict_types=1);

namespace ApiPlatform\SchemaGenerator\DomainGenerator;

class PrimitiveMap
{
    private $map = [
        'xsd:float' => 'float',
        'xsd:string' => 'string',
    ];

    public function getPhpType(string $uri)
    {
        return $this->map[$uri] ?? null;
    }

    /*
    owl:real
    owl:rational
    xsd:decimal
    xsd:integer
    xsd:nonNegativeInteger
    xsd:nonPositiveInteger
    xsd:positiveInteger
    xsd:negativeInteger
    xsd:long
    xsd:int
    xsd:short
    xsd:byte
    xsd:unsignedLong
    xsd:unsignedInt
    xsd:unsignedShort
    xsd:unsignedByte 

    xsd:double
    xsd:float 

    xsd:string
    xsd:normalizedString
    xsd:token
    xsd:language
    xsd:Name
    xsd:NCName
    xsd:NMTOKEN 
    
    xsd:boolean

    xsd:hexBinary
    xsd:base64Binary 
    
    xsd:anyURI

    xsd:dateTime
    xsd:dateTimeStamp 

    rdf:XMLLiteral 

    
    */
}
