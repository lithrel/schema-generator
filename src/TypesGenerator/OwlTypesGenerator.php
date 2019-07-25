<?php

/*
 *
 */

declare(strict_types=1);

namespace ApiPlatform\SchemaGenerator\TypesGenerator;

use ApiPlatform\SchemaGenerator\AbstractTypesGenerator;

class OwlTypesGenerator extends AbstractTypesGenerator
{
    /**
     * @var string
     */
    private const DOMAIN = 'rdfs:domain';

    private const RANGE = 'rdfs:range';

    public $defaultRange = 'Text';

    /**
     * @var array
     */
    protected $resourceTypes = [
        'class' => 'owl:Class',
        'comment' => 'owl:comment',
        'property' => 'owl:ObjectProperty',
        'subClass' => 'rdfs:subClassOf',
    ];

    public function getPropertyRange($property): array
    {
        return $property->all(self::RANGE);
    }

    protected function createPropertiesMap(array $types)
    {
        $typesAsString = [];
        $map = [];
        foreach ($types as $type) {
            // get all parent classes until the root
            $parentClasses = $this->getParentClasses($type);
            $typesAsString[] = $parentClasses;
            $map[$type->getUri()] = [];
        }

        foreach ($this->graphs as $graph) {
            foreach ($graph->allOfType($this->resourceTypes['property']) as $property) {
                foreach ($property->all(self::DOMAIN) as $domain) {
                    foreach ($typesAsString as $typesAsStringItem) {
                        if (in_array($domain->getUri(), $typesAsStringItem, true)) {
                            $map[$typesAsStringItem[0]][] = $property;
                        }
                    }
                }
            }
        }

        return $map;
    }
}