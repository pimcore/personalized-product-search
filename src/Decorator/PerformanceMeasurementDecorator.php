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
use Pimcore\Bundle\PersonalizedSearchBundle\IndexAccessProvider\AdapterPerformanceIndexAccessProvider;

class PerformanceMeasurementDecorator extends AbstractDecorator
{
    /**
     * @var AdapterPerformanceIndexAccessProvider
     */
    private $adapterPerformanceIndex;

    public function __construct(AdapterPerformanceIndexAccessProvider $adapterPerformanceIndex)
    {
        parent::__construct();
        $this->adapterPerformanceIndex = $adapterPerformanceIndex;
    }

    protected function invokeAdapter(AdapterInterface $adapter, array $query): array
    {
        $start = microtime(true);
        $res = $adapter->addPersonalization($query);
        $elapsedTimeInSeconds = microtime(true) - $start;

        $performanceInfo = new PerformanceInfo(get_class($adapter), $elapsedTimeInSeconds * 1000);
        $this->adapterPerformanceIndex->index(0, $performanceInfo);

        return $res;
    }
}
