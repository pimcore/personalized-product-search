<?php

namespace PersonalizedSearchBundle\IndexAccessProvider;

use Elasticsearch\ClientBuilder;

/**
 * Class OrderIndexAccessProvider
 * @package PersonalizedSearchBundle\IndexAccessProvider
 */
class OrderIndexAccessProvider implements IndexAccessProviderInterface
{

    private $esClient;

    /**
     * @var string
     */
    private static $indexName = 'order_segments';

    public function __construct()
    {
        $this->esClient = ClientBuilder::create()->build();
    }

    /**
     * Returns all purchase history segments of a customer
     * @param int $customerId Customer id / user id
     * @return array Segment array
     */
    public function getSegments(int $customerId): array
    {
        $params = [
            'index' => self::$indexName,
            'type' => '_doc',
            'id' => $customerId
        ];

        $response = $this->esClient->get($params);
        return $response['_source']['segments'];
    }

    public function index(int $documentId, array $body)
    {
        // TODO: Implement index() method.
    }
}
