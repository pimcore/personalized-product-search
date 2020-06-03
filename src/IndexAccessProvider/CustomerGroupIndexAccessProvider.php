<?php

namespace Pimcore\Bundle\PersonalizedSearchBundle\IndexAccessProvider;

use Elasticsearch\ClientBuilder;
use Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad\CustomerGroup;
use Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad\CustomerGroupAssignment;
use Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad\SegmentInfo;

/**
 * Class RelevantProductIndexAccessProvider
 * @package PersonalizedSearchBundle\IndexAccessProvider
 */
class CustomerGroupIndexAccessProvider implements CustomerGroupIndexAccessProviderInterface
{
    private $esClient;

    /**
     * @var string
     */
    private static $customerGroupAssignmentIndex = 'customergroup';

    /**
     * @var string
     */
    private static $customerGroupIndex = 'customergroup_segments';

    public function __construct()
    {
        if (class_exists('Elasticsearch\\ClientBuilder')) {
            $this->esClient = ClientBuilder::create()->build();
        }
    }

    private function indexByName(int $documentId, object $body, string $indexName)
    {
        $params = [
            'index' => $indexName,
            'type' => '_doc',
            'id' => $documentId,
            'body' => $body
        ];
        $this->esClient->index($params);
    }

    public function fetchCustomerGroups(): array
    {
        $params = [
            'index' => self::$customerGroupIndex,
            'type' => '_doc',
            'body' => [
                'query' => [
                    'match_all' => (object) []
                ]
            ]
        ];
        $response = $this->esClient->search($params)['hits']['hits'];

        $customerGroupSegments = [];

        foreach ($response as $value) {
            $segmentInfos = array_map(function ($entry) {
                return new SegmentInfo($entry['segmentId'], $entry['segmentCount']);
            }, $value['_source']['segments']);

            $customerGroupSegments[] = new CustomerGroup($value['_source']['customerGroupId'], $segmentInfos);
        }

        return $customerGroupSegments;
    }

    public function indexCustomerGroupAssignment(CustomerGroupAssignment $customerGroupAssignment)
    {
        $obj = new \stdClass();
        $obj->customerId = $customerGroupAssignment->customerId;
        $obj->customerGroupId = $customerGroupAssignment->customerGroup->customerGroupId;
        if(!$this->customerGroupExists($customerGroupAssignment->customerGroup->customerGroupId))
        {
            // create new group if it doesn't exist already
            self::indexByName($customerGroupAssignment->customerGroup->customerGroupId, $customerGroupAssignment->customerGroup, self::$customerGroupIndex);
        }
        self::indexByName($customerGroupAssignment->customerId, $obj , self::$customerGroupAssignmentIndex);
    }

    private function customerGroupExists($customerGroupId)
    {
        $params = [
            'index' => self::$customerGroupIndex,
            'type' => '_doc',
            'body' => [
                'query' => [
                    'match' => [
                        'customerGroupId' => $customerGroupId
                    ]
                ]
            ]
        ];

        $response = $this->esClient->search($params)['hits']['hits'];

        return sizeof($response) === 0 ? false : true;
    }

    public function dropCustomerGroupAssignmentIndex()
    {
        if($this->esClient->indices()->exists(['index' => self::$customerGroupAssignmentIndex]))
            $this->esClient->indices()->delete(['index' => self::$customerGroupAssignmentIndex]);
    }

    public function dropCustomerGroupIndex()
    {
        if($this->esClient->indices()->exists(['index' => self::$customerGroupIndex]))
            $this->esClient->indices()->delete(['index' => self::$customerGroupIndex]);
    }

    public function createCustomerGroupAssignmentIndex()
    {
        if(!$this->esClient->indices()->exists(['index' => self::$customerGroupAssignmentIndex]))
            $this->esClient->indices()->create(['index' => self::$customerGroupAssignmentIndex]);
    }

    public function createCustomerGroupIndex()
    {
        if(!$this->esClient->indices()->exists(['index' => self::$customerGroupIndex]))
            $this->esClient->indices()->create(['index' => self::$customerGroupIndex]);
    }
}
