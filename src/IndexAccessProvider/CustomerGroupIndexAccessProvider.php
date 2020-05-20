<?php

namespace Pimcore\Bundle\PersonalizedSearchBundle\IndexAccessProvider;

use Elasticsearch\ClientBuilder;
use PersonalizedSearchBundle\src\ExtractTransformLoad\CustomerGroupSegments;
use PersonalizedSearchBundle\src\IndexAccessProvider\CustomerGroupIndexAccessProviderInterface;
use Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad\CustomerGroup;

/**
 * Class RelevantProductIndexAccessProvider
 * @package PersonalizedSearchBundle\IndexAccessProvider
 */
class RelevantProductIndexAccessProvider implements CustomerGroupIndexAccessProviderInterface
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
            return [];
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
            return [];
        }

        return new CustomerGroupSegments($customerGroupId, $response[0]['_source']['segments']);
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

    public function fetchCustomerGroupWithSegments($customerId): CustomerGroupSegments
    {
        // TODO: Implement fetchCustomerGroupWithSegments() method.
    }

    public function fetchCustomerGroups(): array
    {
        // TODO: Implement fetchCustomerGroups() method.
    }

    public function fetchCustomerGroupAssignments(): array
    {
        // TODO: Implement fetchCustomerGroupAssignments() method.
    }

    public function fetchCustomerGroupForCustomer($customerId): int
    {
        // TODO: Implement fetchCustomerGroupForCustomer() method.
    }

    public function indexCustomerGroup(CustomerGroup $customerGroup)
    {
        //TODO was wenn es group noch nicht gibt
        $obj = new \stdClass();
        $obj->customerId = $customerGroup->customerId;
        $obj->customerGroupId = $customerGroup->customerGroupSegments->customerGroupId;
        self::indexByName($customerGroup->customerId, $obj , self::$customerGroupIndex);
    }

    public function dropCustomerGroupIndex()
    {
        $this->esClient->indices()->delete(['index' => self::$customerGroupIndex]);
    }

    public function dropCustomerGroupSegmentsIndex()
    {
        $this->esClient->indices()->delete(['index' => self::$customerGroupSegmentIndex]);
    }
}
