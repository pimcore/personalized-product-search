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

namespace Pimcore\Bundle\PersonalizedSearchBundle\Decorator;

use Pimcore\Bundle\PersonalizedSearchBundle\Adapter\AdapterInterface;

abstract class AbstractDecorator implements AdapterInterface
{
    /**
     * @var AdapterInterface[]
     */
    private $adapters;

    public function __construct(array $adapters = [])
    {
        $this->adapters = $adapters;
    }

    public function addAdapter(AdapterInterface $adapter): self
    {
        $this->adapters[] = $adapter;

        return $this;
    }

    public function addPersonalization(array $query, float $weight = 1.0, string $boostMode = 'multiply'): array
    {
        foreach ($this->adapters as $adapter) {
            $query = $this->invokeAdapter($adapter, $query);
        }

        return $query;
    }

    public function getDebugInfo(float $weight = 1.0, string $boostMode = 'multiply'): array
    {
        $res = [];

        foreach ($this->adapters as $adapter) {
            $res[] = $adapter->getDebugInfo($weight, $boostMode);
        }

        return $res;
    }

    abstract protected function invokeAdapter(AdapterInterface $adapter, array $query): array;
}
