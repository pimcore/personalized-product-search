<?php


namespace PersonalizedSearchBundle\src\Decorator;


use Pimcore\Bundle\PersonalizedSearchBundle\Adapter\AdapterInterface;
use Pimcore\Bundle\PersonalizedSearchBundle\Decorator\AbstractDecorator;

class PerformanceMeasurementDecorator extends AbstractDecorator
{

    protected function invokeAdapter(AdapterInterface $adapter, array $query): array
    {
        $start = microtime(true);

        $fp = fopen('AdapterPerformance.txt', 'a');

        $res = $adapter->addPersonalization($query);

        $time_elapsed_secs = microtime(true) - $start;
        fwrite($fp, '\r\n' . get_class($adapter) . $time_elapsed_secs);

        return $res;
    }
}
