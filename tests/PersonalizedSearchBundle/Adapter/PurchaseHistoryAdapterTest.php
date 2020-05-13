<?php

namespace Pimcore\Bundle\PersonalizedSearchBundle\Tests\Adapter;

use PHPUnit\Framework\TestCase;
use Pimcore\Bundle\PersonalizedSearchBundle\Adapter\PurchaseHistoryAdapter;
use Pimcore\Bundle\PersonalizedSearchBundle\Customer\PurchaseHistoryAdapterCustomerIdProvider;
use Pimcore\Bundle\PersonalizedSearchBundle\IndexAccessProvider\OrderIndexAccessProvider;

class PurchaseHistoryAdapterTest extends TestCase
{
    public function testNoUserLoggedIn()
    {
        $queryRed = array ( 'multi_match' => array ( 'query' => 'red', 'type' => 'cross_fields', 'operator' => 'and', 'fields' => array ( 0 => 'attributes.name^4', 1 => 'attributes.name.analyzed', 2 => 'attributes.name.analyzed_ngram', 3 => 'attributes.manufacturer_name^3', 4 => 'attributes.manufacturer_name.analyzed', 5 => 'attributes.manufacturer_name.analyzed_ngram', 6 => 'attributes.color', 7 => 'attributes.carClass', ), ), );
        $expectedPurchaseHistoryBoostedQueryRed = array ( 'multi_match' => array ( 'query' => 'red', 'type' => 'cross_fields', 'operator' => 'and', 'fields' => array ( 0 => 'attributes.name^4', 1 => 'attributes.name.analyzed', 2 => 'attributes.name.analyzed_ngram', 3 => 'attributes.manufacturer_name^3', 4 => 'attributes.manufacturer_name.analyzed', 5 => 'attributes.manufacturer_name.analyzed_ngram', 6 => 'attributes.color', 7 => 'attributes.carClass', ), ), );

        $orderIndexResponse = array ( );
        $customerId = -1;
        $purchaseHistoryAdapter = $this->constructPurchaseHistoryAdapter($customerId, $orderIndexResponse);
        $actualPurchaseHistoryBoostedQueryRed = $purchaseHistoryAdapter->addPersonalization($queryRed);

        self::assertEquals($expectedPurchaseHistoryBoostedQueryRed, $actualPurchaseHistoryBoostedQueryRed);
    }

    public function testCorvetteFanLoggedIn()
    {
        $queryRed = array ( 'multi_match' => array ( 'query' => 'red', 'type' => 'cross_fields', 'operator' => 'and', 'fields' => array ( 0 => 'attributes.name^4', 1 => 'attributes.name.analyzed', 2 => 'attributes.name.analyzed_ngram', 3 => 'attributes.manufacturer_name^3', 4 => 'attributes.manufacturer_name.analyzed', 5 => 'attributes.manufacturer_name.analyzed_ngram', 6 => 'attributes.color', 7 => 'attributes.carClass', ), ), );
        $expectedPurchaseHistoryBoostedQueryRed = array ( 'function_score' => array ( 'query' => array ( 'multi_match' => array ( 'query' => 'red', 'type' => 'cross_fields', 'operator' => 'and', 'fields' => array ( 0 => 'attributes.name^4', 1 => 'attributes.name.analyzed', 2 => 'attributes.name.analyzed_ngram', 3 => 'attributes.manufacturer_name^3', 4 => 'attributes.manufacturer_name.analyzed', 5 => 'attributes.manufacturer_name.analyzed_ngram', 6 => 'attributes.color', 7 => 'attributes.carClass', ), ), ), 'functions' => array ( 0 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 983, ), ), 'weight' => 6.0, ), 1 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 963, ), ), 'weight' => 6.0, ), 2 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 982, ), ), 'weight' => 6.0, ), 3 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 971, ), ), 'weight' => 6.0, ), 4 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 970, ), ), 'weight' => 6.0, ), ), 'boost_mode' => 'multiply', ), );

        $orderIndexResponse = array ( 0 => array ( 'segmentId' => 983, 'segmentCount' => 1, ), 1 => array ( 'segmentId' => 963, 'segmentCount' => 1, ), 2 => array ( 'segmentId' => 982, 'segmentCount' => 1, ), 3 => array ( 'segmentId' => 971, 'segmentCount' => 1, ), 4 => array ( 'segmentId' => 970, 'segmentCount' => 1, ), );
        $customerId = 1021;
        $purchaseHistoryAdapter = $this->constructPurchaseHistoryAdapter($customerId, $orderIndexResponse);
        $actualPurchaseHistoryBoostedQueryRed = $purchaseHistoryAdapter->addPersonalization($queryRed);

        self::assertEquals($expectedPurchaseHistoryBoostedQueryRed, $actualPurchaseHistoryBoostedQueryRed);
    }

    public function testCorvetteFanLoggedInDifferentWeight()
    {
        $queryRed = array ( 'multi_match' => array ( 'query' => 'red', 'type' => 'cross_fields', 'operator' => 'and', 'fields' => array ( 0 => 'attributes.name^4', 1 => 'attributes.name.analyzed', 2 => 'attributes.name.analyzed_ngram', 3 => 'attributes.manufacturer_name^3', 4 => 'attributes.manufacturer_name.analyzed', 5 => 'attributes.manufacturer_name.analyzed_ngram', 6 => 'attributes.color', 7 => 'attributes.carClass', ), ), );
        $expectedPurchaseHistoryBoostedQueryRed = array ( 'function_score' => array ( 'query' => array ( 'multi_match' => array ( 'query' => 'red', 'type' => 'cross_fields', 'operator' => 'and', 'fields' => array ( 0 => 'attributes.name^4', 1 => 'attributes.name.analyzed', 2 => 'attributes.name.analyzed_ngram', 3 => 'attributes.manufacturer_name^3', 4 => 'attributes.manufacturer_name.analyzed', 5 => 'attributes.manufacturer_name.analyzed_ngram', 6 => 'attributes.color', 7 => 'attributes.carClass', ), ), ), 'functions' => array ( 0 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 983, ), ), 'weight' => 72.0, ), 1 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 963, ), ), 'weight' => 72.0, ), 2 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 982, ), ), 'weight' => 72.0, ), 3 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 971, ), ), 'weight' => 72.0, ), 4 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 970, ), ), 'weight' => 72.0, ), ), 'boost_mode' => 'multiply', ), );

        $orderIndexResponse = array ( 0 => array ( 'segmentId' => 983, 'segmentCount' => 1, ), 1 => array ( 'segmentId' => 963, 'segmentCount' => 1, ), 2 => array ( 'segmentId' => 982, 'segmentCount' => 1, ), 3 => array ( 'segmentId' => 971, 'segmentCount' => 1, ), 4 => array ( 'segmentId' => 970, 'segmentCount' => 1, ), );
        $customerId = 1021;
        $purchaseHistoryAdapter = $this->constructPurchaseHistoryAdapter($customerId, $orderIndexResponse);
        $actualPurchaseHistoryBoostedQueryRed = $purchaseHistoryAdapter->addPersonalization($queryRed, 12);

        self::assertEquals($expectedPurchaseHistoryBoostedQueryRed, $actualPurchaseHistoryBoostedQueryRed);
    }

    private function constructPurchaseHistoryAdapter(int $customerId, array $orderIndexResponse) : PurchaseHistoryAdapter
    {
        $orderIndex = $this->getMockBuilder(OrderIndexAccessProvider::class)
            ->setMethods(['fetchSegments'])
            ->getMock();
        $orderIndex->method('fetchSegments')
            ->willReturn($orderIndexResponse);

        $customerIdProvider = $this->getMockBuilder(PurchaseHistoryAdapterCustomerIdProvider::class)
            ->setMethods(['getCustomerId'])
            ->getMock();
        $customerIdProvider->method('getCustomerId')
            ->willReturn($customerId);

        return new PurchaseHistoryAdapter($orderIndex, $customerIdProvider);
    }
}
