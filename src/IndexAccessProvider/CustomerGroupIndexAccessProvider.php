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

    public function fetchCustomerGroupSegments(): array
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

        $customerGroupSegments = [];

        foreach ($response as $value) {
            $customerGroupSegments[] = new CustomerGroupSegments($value['_source']['customerGroupId'], $value['_source']['segments']);
        }

        return $customerGroupSegments;
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
