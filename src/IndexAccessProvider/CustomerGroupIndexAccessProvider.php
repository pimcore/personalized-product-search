<?php

namespace Pimcore\Bundle\PersonalizedSearchBundle\IndexAccessProvider;

use Elasticsearch\ClientBuilder;
use Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad\CustomerGroupSegments;
use Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad\CustomerGroup;

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
    private static $customerGroupIndex = 'customergroup';

    /**
     * @var string
     */
    private static $customerGroupSegmentIndex = 'customergroup_segments';

    public function __construct()
    {
        if (class_exists('Elasticsearch\\ClientBuilder')) {
            $this->esClient = ClientBuilder::create()->build();
        }
    }

    /**
     * Returns all purchase history segments of a customergroup
     * @param int $customerId Customer id / user id
     * @return array Segment array
     */
    public function fetchSegments(int $customerId): array
    {
        return $this->fetchCustomerGroupWithSegments($customerId)->segments;
    }

    public function index(int $documentId, object $body) { }

    public function indexByName(int $documentId, object $body, string $indexName)
    {
        $params = [
            'index' => $indexName,
            'type' => '_doc',
            'id' => $documentId,
            'body' => $body
        ];
        $this->esClient->index($params);
    }

    public function fetchCustomerGroupWithSegments($customerId) : ?CustomerGroupSegments
    {
        $params = [
            'index' => self::$customerGroupIndex,
            'type' => '_doc',
            'body' => [
                'query' => [
                    'match' => [
                        'customerId' => $customerId
                    ]
                ]
            ]
        ];

        $response = $this->esClient->search($params)['hits']['hits'];

        if(sizeof($response) === 0) {
            return null;
        }

        $customerGroupId = $response[0]['_source']['customerGroupId'];

        $params = [
            'index' => self::$customerGroupSegmentIndex,
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

        if(sizeof($response) === 0) {
            return null;
        }

        return new CustomerGroupSegments($customerGroupId, $response[0]['_source']['segments']);
    }

    public function fetchCustomerGroups(): array
    {
        $params = [
            'index' => self::$customerGroupSegmentIndex,
            'type' => '_doc',
            'body' => [
                'query' => [
                    'match_all' => (object) []
                ]
            ]
        ];
        $response = $this->esClient->search($params)['hits']['hits'];

        foreach ($response as $value) {
            $customerGroups[] = $value['customerGroupId'];
        }

        return array_map(function($entry){
            return $entry['_source']['segments']['customerGroupId'];
        }, $response);

    }

    public function fetchCustomerGroupAssignments(): array
    {
        $params = [
            'index' => self::$customerGroupIndex,
            'type' => '_doc'
        ];
        $response = $this->esClient->getSource($params);

        return array_map(function($entry){
            return new CustomerGroup($entry['customerId'], $entry['customerGroupId']);
        }, $response);
    }

    public function fetchCustomerGroupForCustomer($customerId): int
    {
        return $this->fetchCustomerGroupWithSegments($customerId)->customerGroupId;
    }

    public function indexCustomerGroup(CustomerGroup $customerGroup)
    {
        $obj = new \stdClass();
        $obj->customerId = $customerGroup->customerId;
        $obj->customerGroupId = $customerGroup->customerGroupSegments->customerGroupId;
        if(!$this->customerGroupExists($customerGroup->customerGroupSegments->customerGroupId))
        {
            self::indexByName($customerGroup->customerGroupSegments->customerGroupId, $customerGroup->customerGroupSegments, self::$customerGroupSegmentIndex);
        }
        self::indexByName($customerGroup->customerId, $obj , self::$customerGroupIndex);
    }

    public function dropCustomerGroupIndex()
    {
        if($this->esClient->indices()->exists(['index' => self::$customerGroupIndex]))
            $this->esClient->indices()->delete(['index' => self::$customerGroupIndex]);
    }

    public function dropCustomerGroupSegmentsIndex()
    {
        if($this->esClient->indices()->exists(['index' => self::$customerGroupSegmentIndex]))
            $this->esClient->indices()->delete(['index' => self::$customerGroupSegmentIndex]);
    }


    private function customerGroupExists($customerGroupId)
    {
        $params = [
            'index' => self::$customerGroupSegmentIndex,
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

    public function createCustomerGroupIndex()
    {
        if(!$this->esClient->indices()->exists(['index' => self::$customerGroupIndex]))
            $this->esClient->indices()->create(['index' => self::$customerGroupIndex]);
    }

    public function createCustomerGroupSegmentsIndex()
    {
        if(!$this->esClient->indices()->exists(['index' => self::$customerGroupSegmentIndex]))
            $this->esClient->indices()->create(['index' => self::$customerGroupSegmentIndex]);
    }
}
