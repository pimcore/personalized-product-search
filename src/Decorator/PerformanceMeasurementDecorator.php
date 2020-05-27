<?php


namespace PersonalizedSearchBundle\src\Decorator;


use Pimcore\Bundle\PersonalizedSearchBundle\Adapter\AdapterInterface;
use Pimcore\Bundle\PersonalizedSearchBundle\Decorator\AbstractDecorator;

class PerformanceMeasurementDecorator extends AbstractDecorator
{
    const FILE_NAME = "/home/pimcoredemo/AdapterPerformance.txt";

    /*function __construct() {
        parent::__construct();
        file_put_contents(self::FILE_NAME, "");
    }*/

    protected function invokeAdapter(AdapterInterface $adapter, array $query): array
    {
        $start = microtime(true);
        $res = $adapter->addPersonalization($query);
        $time_elapsed_secs = microtime(true) - $start;

        file_put_contents(self::FILE_NAME,  get_class($adapter) . ": " . $time_elapsed_secs * 1000 . "ms" . "\r\n", FILE_APPEND);

        return $res;
    }
}