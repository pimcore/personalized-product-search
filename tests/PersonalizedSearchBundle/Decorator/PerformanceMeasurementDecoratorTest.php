<?php


namespace PersonalizedSearchBundle\tests\PersonalizedSearchBundle\Decorator;


use phpDocumentor\Reflection\Types\Array_;
use PHPUnit\Framework\TestCase;
use CustomerManagementFrameworkBundle\Targeting\SegmentTracker;
use Pimcore\Bundle\PersonalizedSearchBundle\Adapter\AdapterInterface;
use Pimcore\Bundle\PersonalizedSearchBundle\Adapter\SegmentAdapter;
use Pimcore\Bundle\PersonalizedSearchBundle\Decorator\PerformanceMeasurementDecorator;
use Pimcore\Bundle\PersonalizedSearchBundle\IndexAccessProvider\AdapterPerformanceIndexAccessProvider;
use Pimcore\Targeting\VisitorInfoStorage;

class PerformanceMeasurementDecoratorTest extends TestCase
{
    private $measuredTime = -1;

    public function testCorrectMeasurement() {
        $decorator = $this->constructPerformanceMeasurementDecorator();
        $decorator->addAdapter($this->constructAdapter());
        $decorator->addPersonalization([]);

        // there is always some overhead
        $this->assertTrue($this->measuredTime > 1000);
    }

    private function constructPerformanceMeasurementDecorator() : PerformanceMeasurementDecorator
    {
        $accessProvider = $this->getMockBuilder(AdapterPerformanceIndexAccessProvider::class)
            ->setMethods(['index'])
            ->getMock();
        $accessProvider->method('index')
            ->will($this->returnCallback(function($adapter, $obj) {
                $this->measuredTime = $obj->elapsedTime;
            }));


        return new PerformanceMeasurementDecorator($accessProvider);
    }

    // it doesn't matter which adapter implementation is used here
    private function constructAdapter() : AdapterInterface
    {
        $adapter = $this->getMockBuilder(AdapterInterface::class)
            ->setMethods(['addPersonalization'])
            ->getMock();
        $adapter->method('addPersonalization')
            ->will($this->returnCallback(function() {
                sleep(1);
                return [];
            }));

        return $adapter;
    }
}
