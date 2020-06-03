<?php


namespace Pimcore\Bundle\PersonalizedSearchBundle\Decorator;

use Pimcore\Bundle\PersonalizedSearchBundle\Adapter\AdapterInterface;
use Pimcore\Bundle\PersonalizedSearchBundle\IndexAccessProvider\AdapterPerformanceIndexAccessProvider;

class PerformanceMeasurementDecorator extends AbstractDecorator
{
    const FILE_NAME = "/home/pimcoredemo/AdapterPerformance.txt";

    /**
     * @var AdapterPerformanceIndexAccessProvider
     */
    private $adapterPerformanceIndex;

    function __construct(AdapterPerformanceIndexAccessProvider $adapterPerformanceIndex) {
        parent::__construct();
        $this->adapterPerformanceIndex = $adapterPerformanceIndex;
//        file_put_contents(self::FILE_NAME, "-----------------------------------------\r\n", FILE_APPEND);
    }

    protected function invokeAdapter(AdapterInterface $adapter, array $query): array
    {
        $start = microtime(true);
        $res = $adapter->addPersonalization($query);
        $elapsedTimeInSeconds = microtime(true) - $start;

        $performanceInfo = new PerformanceInfo(get_class($adapter), $elapsedTimeInSeconds * 1000);
        $this->adapterPerformanceIndex->index(0, $performanceInfo);

//        print_r("--- before print\n");
//        $hugo = date('Y-m-d H:i:s') . ": " . get_class($adapter) . ": " . $elapsedTimeInSeconds * 1000 . "ms" . "\n";
//        print_r("text: " . $hugo . "\n");
//        file_put_contents(self::FILE_NAME,
//            $hugo,
//            FILE_APPEND);
//        print_r("after print --- \n");

        return $res;
    }
}
