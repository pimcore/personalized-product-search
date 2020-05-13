<?php

namespace Pimcore\Bundle\PersonalizedSearchBundle\IndexAccessProvider;

use Elasticsearch\ClientBuilder;

/**
 * Class RelevantProductIndexAccessProvider
 * @package PersonalizedSearchBundle\IndexAccessProvider
 */
class RelevantProductIndexAccessProvider implements IndexAccessProviderInterface
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
        $this->esClient = ClientBuilder::create()->build();
    }

    /**
     * Returns all purchase history segments of a customer
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

        return $response[0]['_source']['segments'];
    }

    public function index(int $documentId, object $body)
    {
        // TODO: needs to be implemented
    }
}
