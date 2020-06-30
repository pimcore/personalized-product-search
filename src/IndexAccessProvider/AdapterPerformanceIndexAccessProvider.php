<?php


namespace Pimcore\Bundle\PersonalizedSearchBundle\IndexAccessProvider;

class AdapterPerformanceIndexAccessProvider extends EsAwareIndexAccessProvider implements IndexAccessProviderInterface
{
    /**
     * @var string
     */
    private static $indexName = 'adapter_performance';

    public function fetchSegments(int $customerId): array
    {
        return [];
    }


    public function index(int $documentId, object $body)
    {
        $params = [
            'index' => $this->indexPrefix . self::$indexName,
            'type' => '_doc',
            'body' => $body
        ];
        $this->esClient->index($params);
    }
}
