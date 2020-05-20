<?php

namespace Pimcore\Bundle\PersonalizedSearchBundle\Tests\Decorator;

use CustomerManagementFrameworkBundle\Targeting\SegmentTracker;
use PHPUnit\Framework\TestCase;
use Pimcore\Bundle\PersonalizedSearchBundle\Adapter\PurchaseHistoryAdapter;
use Pimcore\Bundle\PersonalizedSearchBundle\Adapter\SegmentAdapter;
use Pimcore\Bundle\PersonalizedSearchBundle\Customer\PersonalizationAdapterCustomerIdProvider;
use Pimcore\Bundle\PersonalizedSearchBundle\Decorator\EqualWeightDecorator;
use Pimcore\Bundle\PersonalizedSearchBundle\IndexAccessProvider\OrderIndexAccessProvider;
use Pimcore\Targeting\VisitorInfoStorage;

class EqualWeightDecoratorTest extends TestCase
{
    public function testSegmentBasedAdapterOnly()
    {
        $queryRed = array ( 'multi_match' => array ( 'query' => 'red', 'type' => 'cross_fields', 'operator' => 'and', 'fields' => array ( 0 => 'attributes.name^4', 1 => 'attributes.name.analyzed', 2 => 'attributes.name.analyzed_ngram', 3 => 'attributes.manufacturer_name^3', 4 => 'attributes.manufacturer_name.analyzed', 5 => 'attributes.manufacturer_name.analyzed_ngram', 6 => 'attributes.color', 7 => 'attributes.carClass', ), ), );
        $expectedSegmentBoostedQueryRed = array ( 'function_score' => array ( 'query' => array ( 'multi_match' => array ( 'query' => 'red', 'type' => 'cross_fields', 'operator' => 'and', 'fields' => array ( 0 => 'attributes.name^4', 1 => 'attributes.name.analyzed', 2 => 'attributes.name.analyzed_ngram', 3 => 'attributes.manufacturer_name^3', 4 => 'attributes.manufacturer_name.analyzed', 5 => 'attributes.manufacturer_name.analyzed_ngram', 6 => 'attributes.color', 7 => 'attributes.carClass', ), ), ), 'functions' => array ( 0 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 860, ), ), 'weight' => 1.0, ), 1 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 966, ), ), 'weight' => 7.0, ), 2 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 967, ), ), 'weight' => 7.0, ), 3 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 968, ), ), 'weight' => 7.0, ), ), 'boost_mode' => 'multiply', ), );

        $equalWeightDecorator = new EqualWeightDecorator();
        $segmentAdapter = $this->constructSegmentAdapter();
        $equalWeightDecorator->addAdapter($segmentAdapter);

        $actualSegmentAdapterBoostedQueryRed = $equalWeightDecorator->addPersonalization($queryRed);
        self::assertEquals($expectedSegmentBoostedQueryRed, $actualSegmentAdapterBoostedQueryRed);
    }

    public function testPurchaseHistoryAdapterOnly()
    {
        $queryRed = array ( 'multi_match' => array ( 'query' => 'red', 'type' => 'cross_fields', 'operator' => 'and', 'fields' => array ( 0 => 'attributes.name^4', 1 => 'attributes.name.analyzed', 2 => 'attributes.name.analyzed_ngram', 3 => 'attributes.manufacturer_name^3', 4 => 'attributes.manufacturer_name.analyzed', 5 => 'attributes.manufacturer_name.analyzed_ngram', 6 => 'attributes.color', 7 => 'attributes.carClass', ), ), );
        $expectedPurchaseHistoryBoostedQueryRed = array ( 'function_score' => array ( 'query' => array ( 'multi_match' => array ( 'query' => 'red', 'type' => 'cross_fields', 'operator' => 'and', 'fields' => array ( 0 => 'attributes.name^4', 1 => 'attributes.name.analyzed', 2 => 'attributes.name.analyzed_ngram', 3 => 'attributes.manufacturer_name^3', 4 => 'attributes.manufacturer_name.analyzed', 5 => 'attributes.manufacturer_name.analyzed_ngram', 6 => 'attributes.color', 7 => 'attributes.carClass', ), ), ), 'functions' => array ( 0 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 983, ), ), 'weight' => 6.0, ), 1 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 963, ), ), 'weight' => 6.0, ), 2 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 982, ), ), 'weight' => 6.0, ), 3 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 971, ), ), 'weight' => 6.0, ), 4 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 970, ), ), 'weight' => 6.0, ), ), 'boost_mode' => 'multiply', ), );

        $equalWeightDecorator = new EqualWeightDecorator();
        $orderIndexResponse = array ( 0 => array ( 'segmentId' => 983, 'segmentCount' => 1, ), 1 => array ( 'segmentId' => 963, 'segmentCount' => 1, ), 2 => array ( 'segmentId' => 982, 'segmentCount' => 1, ), 3 => array ( 'segmentId' => 971, 'segmentCount' => 1, ), 4 => array ( 'segmentId' => 970, 'segmentCount' => 1, ), );
        $customerId = 1021;
        $purchaseHistoryAdapter = $this->constructPurchaseHistoryAdapter($customerId, $orderIndexResponse);
        $equalWeightDecorator->addAdapter($purchaseHistoryAdapter);

        $actualPurchaseHistoryBoostedQueryRed = $equalWeightDecorator->addPersonalization($queryRed);
        self::assertEquals($expectedPurchaseHistoryBoostedQueryRed, $actualPurchaseHistoryBoostedQueryRed);
    }

    public function testSegmentAdapterFirstPurchaseHistoryAdapterSecond()
    {
        $queryRed = array ( 'multi_match' => array ( 'query' => 'red', 'type' => 'cross_fields', 'operator' => 'and', 'fields' => array ( 0 => 'attributes.name^4', 1 => 'attributes.name.analyzed', 2 => 'attributes.name.analyzed_ngram', 3 => 'attributes.manufacturer_name^3', 4 => 'attributes.manufacturer_name.analyzed', 5 => 'attributes.manufacturer_name.analyzed_ngram', 6 => 'attributes.color', 7 => 'attributes.carClass', ), ), );
        $expectedBoostedQueryRed = array ( 'function_score' => array ( 'query' => array ( 'function_score' => array ( 'query' => array ( 'multi_match' => array ( 'query' => 'red', 'type' => 'cross_fields', 'operator' => 'and', 'fields' => array ( 0 => 'attributes.name^4', 1 => 'attributes.name.analyzed', 2 => 'attributes.name.analyzed_ngram', 3 => 'attributes.manufacturer_name^3', 4 => 'attributes.manufacturer_name.analyzed', 5 => 'attributes.manufacturer_name.analyzed_ngram', 6 => 'attributes.color', 7 => 'attributes.carClass', ), ), ), 'functions' => array ( 0 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 860, ), ), 'weight' => 1.0, ), 1 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 966, ), ), 'weight' => 7.0, ), 2 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 967, ), ), 'weight' => 7.0, ), 3 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 968, ), ), 'weight' => 7.0, ), ), 'boost_mode' => 'multiply', ), ), 'functions' => array ( 0 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 983, ), ), 'weight' => 6.0, ), 1 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 963, ), ), 'weight' => 6.0, ), 2 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 982, ), ), 'weight' => 6.0, ), 3 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 971, ), ), 'weight' => 6.0, ), 4 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 970, ), ), 'weight' => 6.0, ), ), 'boost_mode' => 'multiply', ), );

        $equalWeightDecorator = new EqualWeightDecorator();

        $segmentAdapter = $this->constructSegmentAdapter();
        $equalWeightDecorator->addAdapter($segmentAdapter);

        $orderIndexResponse = array ( 0 => array ( 'segmentId' => 983, 'segmentCount' => 1, ), 1 => array ( 'segmentId' => 963, 'segmentCount' => 1, ), 2 => array ( 'segmentId' => 982, 'segmentCount' => 1, ), 3 => array ( 'segmentId' => 971, 'segmentCount' => 1, ), 4 => array ( 'segmentId' => 970, 'segmentCount' => 1, ), );
        $customerId = 1021;
        $purchaseHistoryAdapter = $this->constructPurchaseHistoryAdapter($customerId, $orderIndexResponse);
        $equalWeightDecorator->addAdapter($purchaseHistoryAdapter);

        $actualBoostedQueryRed = $equalWeightDecorator->addPersonalization($queryRed);
        self::assertEquals($expectedBoostedQueryRed, $actualBoostedQueryRed);
    }

    public function testPurchaseHistoryAdapterFirstSegmentAdapterSecond()
    {
        $queryRed = array ( 'multi_match' => array ( 'query' => 'red', 'type' => 'cross_fields', 'operator' => 'and', 'fields' => array ( 0 => 'attributes.name^4', 1 => 'attributes.name.analyzed', 2 => 'attributes.name.analyzed_ngram', 3 => 'attributes.manufacturer_name^3', 4 => 'attributes.manufacturer_name.analyzed', 5 => 'attributes.manufacturer_name.analyzed_ngram', 6 => 'attributes.color', 7 => 'attributes.carClass', ), ), );
        $expectedBoostedQueryRed = array ( 'function_score' => array ( 'query' => array ( 'function_score' => array ( 'query' => array ( 'multi_match' => array ( 'query' => 'red', 'type' => 'cross_fields', 'operator' => 'and', 'fields' => array ( 0 => 'attributes.name^4', 1 => 'attributes.name.analyzed', 2 => 'attributes.name.analyzed_ngram', 3 => 'attributes.manufacturer_name^3', 4 => 'attributes.manufacturer_name.analyzed', 5 => 'attributes.manufacturer_name.analyzed_ngram', 6 => 'attributes.color', 7 => 'attributes.carClass', ), ), ), 'functions' => array ( 0 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 983, ), ), 'weight' => 6.0, ), 1 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 963, ), ), 'weight' => 6.0, ), 2 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 982, ), ), 'weight' => 6.0, ), 3 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 971, ), ), 'weight' => 6.0, ), 4 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 970, ), ), 'weight' => 6.0, ), ), 'boost_mode' => 'multiply', ), ), 'functions' => array ( 0 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 860, ), ), 'weight' => 1.0, ), 1 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 966, ), ), 'weight' => 7.0, ), 2 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 967, ), ), 'weight' => 7.0, ), 3 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 968, ), ), 'weight' => 7.0, ), ), 'boost_mode' => 'multiply', ), );

        $equalWeightDecorator = new EqualWeightDecorator();

        $orderIndexResponse = array ( 0 => array ( 'segmentId' => 983, 'segmentCount' => 1, ), 1 => array ( 'segmentId' => 963, 'segmentCount' => 1, ), 2 => array ( 'segmentId' => 982, 'segmentCount' => 1, ), 3 => array ( 'segmentId' => 971, 'segmentCount' => 1, ), 4 => array ( 'segmentId' => 970, 'segmentCount' => 1, ), );
        $customerId = 1021;
        $purchaseHistoryAdapter = $this->constructPurchaseHistoryAdapter($customerId, $orderIndexResponse);
        $equalWeightDecorator->addAdapter($purchaseHistoryAdapter);

        $segmentAdapter = $this->constructSegmentAdapter();
        $equalWeightDecorator->addAdapter($segmentAdapter);

        $actualBoostedQueryRed = $equalWeightDecorator->addPersonalization($queryRed);
        self::assertEquals($expectedBoostedQueryRed, $actualBoostedQueryRed);
    }

    private function constructSegmentAdapter() : SegmentAdapter
    {
        $visitorInfoStorage = $this
            ->getMockBuilder(VisitorInfoStorage::class)
            ->setMethods(['getVisitorInfo'])
            ->getMock();
        $visitorInfoStorage->method('getVisitorInfo')
            ->willReturn(null);

        $segmentTracker = $this
            ->getMockBuilder(SegmentTracker::class)
            ->setMethods(['getAssignments'])
            ->getMock();
        $segmentTracker->method('getAssignments')
            ->willReturn(array ( 860 => 1, 966 => 7, 967 => 7, 968 => 7, ));

        return new SegmentAdapter($visitorInfoStorage, $segmentTracker);
    }

    private function constructPurchaseHistoryAdapter(int $customerId, array $orderIndexResponse) : PurchaseHistoryAdapter
    {
        $orderIndex = $this->getMockBuilder(OrderIndexAccessProvider::class)
            ->setMethods(['fetchSegments'])
            ->getMock();
        $orderIndex->method('fetchSegments')
            ->willReturn($orderIndexResponse);

        $customerIdProvider = $this->getMockBuilder(PersonalizationAdapterCustomerIdProvider::class)
            ->setMethods(['getCustomerId'])
            ->getMock();
        $customerIdProvider->method('getCustomerId')
            ->willReturn($customerId);

        return new PurchaseHistoryAdapter($orderIndex, $customerIdProvider);
    }
}
