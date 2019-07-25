<?php

declare(strict_types=1);

namespace ApiPlatform\SchemaGenerator\Owl;

use ApiPlatform\SchemaGenerator\Owl;

/**
 * 
 */
class NodeType
{
    // Instance object
    const LITERAL = 'node:literal';
    const COLLECTION = 'node:collection';


    // Datatype
    const DATATYPE = 'rdfs:Datatype';


    // Class
    const CLASS_DEFINITION = 'owl:Class';
    const LABEL = 'rdfs:label';
    const COMMENT = 'rdfs:comment';
    const SUBCLASS_OF = 'rdfs:subClassOf';
    // Restriction
    const RESTRICTION = 'owl:Restriction';
    const ON_PROPERTY = 'owl:onProperty';
    const ALL_VALUES_FROM = 'owl:allValuesFrom';
    // Constraint facet
    const MIN_CARDINALITY = 'owl:minCardinality';
    const MAX_CARDINALITY = 'owl:maxCardinality';
    const EXACT_CARDINALITY = 'owl:exactCardinality';


    const OBJECT_PROPERTY = 'owl:ObjectProperty';
    const DATATYPE_PROPERTY = 'owl:DatatypeProperty';


    const DOMAIN = 'rdfs:domain';
    const SUBPROPERTY_OF = 'rdfs:subPropertyOf';


    // Range
    const RANGE = 'rdfs:range';
    const INTERSECTION_OF = 'owl:intersectionOf';
    const COMPLEMENT_OF = 'owl:complementOf';
    const UNION_OF = 'owl:unionOf';
    const ONE_OF = 'owl:oneOf';
    // Restriction
    const WITH_RESTRICTION = 'owl:withRestrictions';
    const RESTRICTION_DATATYPE = 'owl:onDatatype';
    // Constraint facet
    const XSD_MIN_INCLUSIVE = 'xsd:minInclusive';
    const XSD_MAX_INCLUSIVE = 'xsd:maxInclusive';
    const XSD_MIN_EXCLUSIVE = 'xsd:minExclusive';
    const XSD_MAX_EXCLUSIVE = 'xsd:maxExclusive';
}
