<?php


namespace Pimcore\Bundle\PersonalizedSearchBundle\Tests\Adapter;

use PHPUnit\Framework\TestCase;
use Pimcore\Bundle\PersonalizedSearchBundle\Adapter\RelevantProductsAdapter;
use Pimcore\Bundle\PersonalizedSearchBundle\Customer\PersonalizationAdapterCustomerIdProvider;
use Pimcore\Bundle\PersonalizedSearchBundle\IndexAccessProvider\CustomerGroupIndexAccessProvider;

class RelevantProductsAdapterTest extends TestCase
{
    public function testNoUserLoggedIn()
    {
        $queryRed = array ( 'multi_match' => array ( 'query' => 'red', 'type' => 'cross_fields', 'operator' => 'and', 'fields' => array ( 0 => 'attributes.name^4', 1 => 'attributes.name.analyzed', 2 => 'attributes.name.analyzed_ngram', 3 => 'attributes.manufacturer_name^3', 4 => 'attributes.manufacturer_name.analyzed', 5 => 'attributes.manufacturer_name.analyzed_ngram', 6 => 'attributes.color', 7 => 'attributes.carClass', ), ), );
        $expectedRelevantProductsBoostedQueryRed = array ( 'multi_match' => array ( 'query' => 'red', 'type' => 'cross_fields', 'operator' => 'and', 'fields' => array ( 0 => 'attributes.name^4', 1 => 'attributes.name.analyzed', 2 => 'attributes.name.analyzed_ngram', 3 => 'attributes.manufacturer_name^3', 4 => 'attributes.manufacturer_name.analyzed', 5 => 'attributes.manufacturer_name.analyzed_ngram', 6 => 'attributes.color', 7 => 'attributes.carClass', ), ), );

        $relevantProductsIndexResponse = array ( );
        $customerId = -1;
        $relevantProductsAdapter = $this->constructRelevantProductsAdapter($customerId, $relevantProductsIndexResponse);
        $actualRelevantProductsBoostedQueryRed = $relevantProductsAdapter->addPersonalization($queryRed);

        self::assertEquals($expectedRelevantProductsBoostedQueryRed, $actualRelevantProductsBoostedQueryRed);
    }

    public function testCorvetteFanLoggedIn()
    {
        $queryRed = array ( 'multi_match' => array ( 'query' => 'red', 'type' => 'cross_fields', 'operator' => 'and', 'fields' => array ( 0 => 'attributes.name^4', 1 => 'attributes.name.analyzed', 2 => 'attributes.name.analyzed_ngram', 3 => 'attributes.manufacturer_name^3', 4 => 'attributes.manufacturer_name.analyzed', 5 => 'attributes.manufacturer_name.analyzed_ngram', 6 => 'attributes.color', 7 => 'attributes.carClass', ), ), );
        $expectedRelevantProductsBoostedQueryRed = array ( 'function_score' => array ( 'query' => array ( 'multi_match' => array ( 'query' => 'red', 'type' => 'cross_fields', 'operator' => 'and', 'fields' => array ( 0 => 'attributes.name^4', 1 => 'attributes.name.analyzed', 2 => 'attributes.name.analyzed_ngram', 3 => 'attributes.manufacturer_name^3', 4 => 'attributes.manufacturer_name.analyzed', 5 => 'attributes.manufacturer_name.analyzed_ngram', 6 => 'attributes.color', 7 => 'attributes.carClass', ), ), ), 'functions' => array ( 0 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 983, ), ), 'weight' => 8.0, ), 1 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 963, ), ), 'weight' => 8.0, ), 2 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 982, ), ), 'weight' => 8.0, ), 3 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 971, ), ), 'weight' => 8.0, ), 4 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 970, ), ), 'weight' => 8.0, ), ), 'boost_mode' => 'multiply', ), );

        $relevantProductsIndexResponse = array ( 0 => array ( 'segmentId' => 983, 'segmentCount' => 1, ), 1 => array ( 'segmentId' => 963, 'segmentCount' => 1, ), 2 => array ( 'segmentId' => 982, 'segmentCount' => 1, ), 3 => array ( 'segmentId' => 971, 'segmentCount' => 1, ), 4 => array ( 'segmentId' => 970, 'segmentCount' => 1, ), );
        $customerId = 1021;
        $purchaseHistoryAdapter = $this->constructRelevantProductsAdapter($customerId, $relevantProductsIndexResponse);
        $actualRelevantProductsBoostedQueryRed = $purchaseHistoryAdapter->addPersonalization($queryRed);

        self::assertEquals($expectedRelevantProductsBoostedQueryRed, $actualRelevantProductsBoostedQueryRed);
    }

    public function testCorvetteFanLoggedInDifferentWeight()
    {
        $queryRed = array ( 'multi_match' => array ( 'query' => 'red', 'type' => 'cross_fields', 'operator' => 'and', 'fields' => array ( 0 => 'attributes.name^4', 1 => 'attributes.name.analyzed', 2 => 'attributes.name.analyzed_ngram', 3 => 'attributes.manufacturer_name^3', 4 => 'attributes.manufacturer_name.analyzed', 5 => 'attributes.manufacturer_name.analyzed_ngram', 6 => 'attributes.color', 7 => 'attributes.carClass', ), ), );
        $expectedRelevantProductsBoostedQueryRed = array ( 'function_score' => array ( 'query' => array ( 'multi_match' => array ( 'query' => 'red', 'type' => 'cross_fields', 'operator' => 'and', 'fields' => array ( 0 => 'attributes.name^4', 1 => 'attributes.name.analyzed', 2 => 'attributes.name.analyzed_ngram', 3 => 'attributes.manufacturer_name^3', 4 => 'attributes.manufacturer_name.analyzed', 5 => 'attributes.manufacturer_name.analyzed_ngram', 6 => 'attributes.color', 7 => 'attributes.carClass', ), ), ), 'functions' => array ( 0 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 983, ), ), 'weight' => 96.0, ), 1 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 963, ), ), 'weight' => 96.0, ), 2 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 982, ), ), 'weight' => 96.0, ), 3 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 971, ), ), 'weight' => 96.0, ), 4 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 970, ), ), 'weight' => 96.0, ), ), 'boost_mode' => 'multiply', ), );

        $relevantProductsIndexResponse = array ( 0 => array ( 'segmentId' => 983, 'segmentCount' => 1, ), 1 => array ( 'segmentId' => 963, 'segmentCount' => 1, ), 2 => array ( 'segmentId' => 982, 'segmentCount' => 1, ), 3 => array ( 'segmentId' => 971, 'segmentCount' => 1, ), 4 => array ( 'segmentId' => 970, 'segmentCount' => 1, ), );
        $customerId = 1021;
        $purchaseHistoryAdapter = $this->constructRelevantProductsAdapter($customerId, $relevantProductsIndexResponse);
        $actualRelevantProductsBoostedQueryRed = $purchaseHistoryAdapter->addPersonalization($queryRed, 12);

        self::assertEquals($expectedRelevantProductsBoostedQueryRed, $actualRelevantProductsBoostedQueryRed);
    }

    private function constructRelevantProductsAdapter(int $customerId, array $relevantProductsIndexResponse) : RelevantProductsAdapter
    {
        $relevantProductsIndex = $this->getMockBuilder(CustomerGroupIndexAccessProvider::class)
            ->setMethods(['fetchSegments'])
            ->getMock();
        $relevantProductsIndex->method('fetchSegments')
            ->willReturn($relevantProductsIndexResponse);

        $customerIdProvider = $this->getMockBuilder(PersonalizationAdapterCustomerIdProvider::class)
            ->setMethods(['getCustomerId'])
            ->getMock();
        $customerIdProvider->method('getCustomerId')
            ->willReturn($customerId);

        return new RelevantProductsAdapter($relevantProductsIndex, $customerIdProvider);
    }
}
