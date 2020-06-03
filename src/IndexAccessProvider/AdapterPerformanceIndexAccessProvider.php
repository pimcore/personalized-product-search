<?php


namespace Pimcore\Bundle\PersonalizedSearchBundle\IndexAccessProvider;

use Elasticsearch\ClientBuilder;

/**
 * Class AdapterPerformanceIndexAccessProvider
 * @package PersonalizedSearchBundle\IndexAccessProvider
 */
class AdapterPerformanceIndexAccessProvider implements IndexAccessProviderInterface
{
    private $esClient;

    /**
     * @var string
     */
    private static $indexName = 'adapter_performance';

    public function __construct()
    {
        if (class_exists('Elasticsearch\\ClientBuilder')) {
            $this->esClient = ClientBuilder::create()->build();
        }
    }

    public function fetchSegments(int $customerId): array
    {
        return [];
    }


    public function index(int $documentId, object $body)
    {
        $params = [
            'index' => self::$indexName,
            'type' => '_doc',
            'body' => $body
        ];
        $this->esClient->index($params);
    }
}