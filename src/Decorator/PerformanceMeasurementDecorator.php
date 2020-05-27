<?php


namespace PersonalizedSearchBundle\src\Decorator;


use Pimcore\Bundle\PersonalizedSearchBundle\Adapter\AdapterInterface;
use Pimcore\Bundle\PersonalizedSearchBundle\Decorator\AbstractDecorator;

class PerformanceMeasurementDecorator extends AbstractDecorator
{

    protected function invokeAdapter(AdapterInterface $adapter, array $query): array
    {
        $start = microtime(true);
        $res = $adapter->addPersonalization($query);
        $time_elapsed_secs = microtime(true) - $start;

        file_put_contents("/home/pimcoredemo/AdapterPerformance.txt", "\r\n" . get_class($adapter) . ": " . $time_elapsed_secs * 1000 . "ms", FILE_APPEND);

        return $res;
    }
}
