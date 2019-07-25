<?php

/*
 * This file is part of the API Platform project.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace ApiPlatform\SchemaGenerator;

use Psr\Log\LoggerInterface;

interface TypesGeneratorInterface
{
    public function __construct(\Twig_Environment $twig, LoggerInterface $logger, array $graphs, CardinalitiesExtractor $cardinalitiesExtractor, GoodRelationsBridge $goodRelationsBridge);

    public function generate(array $config): void;
}