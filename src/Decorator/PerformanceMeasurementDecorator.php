<?php


namespace Pimcore\Bundle\PersonalizedSearchBundle\Decorator;

use Pimcore\Bundle\PersonalizedSearchBundle\Adapter\AdapterInterface;
use Pimcore\Bundle\PersonalizedSearchBundle\IndexAccessProvider\AdapterPerformanceIndexAccessProvider;

class PerformanceMeasurementDecorator extends AbstractDecorator
{

    /**
     * @var AdapterPerformanceIndexAccessProvider
     */
    private $adapterPerformanceIndex;

    function __construct(AdapterPerformanceIndexAccessProvider $adapterPerformanceIndex) {
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
