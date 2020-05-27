<?php

namespace Pimcore\Bundle\PersonalizedSearchBundle\Tests\Adapter;

use CustomerManagementFrameworkBundle\Targeting\SegmentTracker;
use PHPUnit\Framework\TestCase;
use Pimcore\Bundle\PersonalizedSearchBundle\Adapter\SegmentAdapter;
use Pimcore\Targeting\VisitorInfoStorage;

class SegmentAdapterTest extends TestCase
{
    public function testSegmentBasedAdapter()
    {
        $queryRed = array ( 'multi_match' => array ( 'query' => 'red', 'type' => 'cross_fields', 'operator' => 'and', 'fields' => array ( 0 => 'attributes.name^4', 1 => 'attributes.name.analyzed', 2 => 'attributes.name.analyzed_ngram', 3 => 'attributes.manufacturer_name^3', 4 => 'attributes.manufacturer_name.analyzed', 5 => 'attributes.manufacturer_name.analyzed_ngram', 6 => 'attributes.color', 7 => 'attributes.carClass', ), ), );
        $expectedSegmentBoostedQueryRed = array ( 'function_score' => array ( 'query' => array ( 'multi_match' => array ( 'query' => 'red', 'type' => 'cross_fields', 'operator' => 'and', 'fields' => array ( 0 => 'attributes.name^4', 1 => 'attributes.name.analyzed', 2 => 'attributes.name.analyzed_ngram', 3 => 'attributes.manufacturer_name^3', 4 => 'attributes.manufacturer_name.analyzed', 5 => 'attributes.manufacturer_name.analyzed_ngram', 6 => 'attributes.color', 7 => 'attributes.carClass', ), ), ), 'functions' => array ( 0 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 860, ), ), 'weight' => 1.0, ), 1 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 966, ), ), 'weight' => 6.0, ), 2 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 967, ), ), 'weight' => 6.0, ), 3 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 968, ), ), 'weight' => 6.0, ), ), 'boost_mode' => 'multiply', ), );

        $segmentAdapter = $this->constructSegmentAdapter();
        $actualSegmentBoostedQueryRed = $segmentAdapter->addPersonalization($queryRed);

        self::assertEquals($expectedSegmentBoostedQueryRed, $actualSegmentBoostedQueryRed);
    }

    public function testSegmentBasedAdapterWithDifferentWeighting()
    {
        $queryRed = array ( 'multi_match' => array ( 'query' => 'red', 'type' => 'cross_fields', 'operator' => 'and', 'fields' => array ( 0 => 'attributes.name^4', 1 => 'attributes.name.analyzed', 2 => 'attributes.name.analyzed_ngram', 3 => 'attributes.manufacturer_name^3', 4 => 'attributes.manufacturer_name.analyzed', 5 => 'attributes.manufacturer_name.analyzed_ngram', 6 => 'attributes.color', 7 => 'attributes.carClass', ), ), );
        $expectedSegmentBoostedQueryRed = array ( 'function_score' => array ( 'query' => array ( 'multi_match' => array ( 'query' => 'red', 'type' => 'cross_fields', 'operator' => 'and', 'fields' => array ( 0 => 'attributes.name^4', 1 => 'attributes.name.analyzed', 2 => 'attributes.name.analyzed_ngram', 3 => 'attributes.manufacturer_name^3', 4 => 'attributes.manufacturer_name.analyzed', 5 => 'attributes.manufacturer_name.analyzed_ngram', 6 => 'attributes.color', 7 => 'attributes.carClass', ), ), ), 'functions' => array ( 0 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 860, ), ), 'weight' => 2.0, ), 1 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 966, ), ), 'weight' => 12.0, ), 2 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 967, ), ), 'weight' => 12.0, ), 3 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 968, ), ), 'weight' => 12.0, ), ), 'boost_mode' => 'multiply', ), );

        $segmentAdapter = $this->constructSegmentAdapter();
        $actualSegmentBoostedQueryRed = $segmentAdapter->addPersonalization($queryRed, 2);

        self::assertEquals($expectedSegmentBoostedQueryRed, $actualSegmentBoostedQueryRed);
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
            ->willReturn(array ( 860 => 1, 966 => 6, 967 => 6, 968 => 6, ));

        return new SegmentAdapter($visitorInfoStorage, $segmentTracker);
    }
}
