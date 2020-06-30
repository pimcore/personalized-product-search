<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace Pimcore\Bundle\PersonalizedSearchBundle\Adapter;

interface AdapterInterface
{
    public function addPersonalization(array $query, float $weight = 1.0, string $boostMode = 'multiply'): array;

    public function getDebugInfo(float $weight = 1.0, string $boostMode = 'multiply'): array;
}
