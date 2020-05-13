<?php


namespace Pimcore\Bundle\PersonalizedSearchBundle\Adapter;

use Pimcore\Bundle\EcommerceFrameworkBundle\Factory;
use Pimcore\Bundle\PersonalizedSearchBundle\IndexAccessProvider\RelevantProductIndexAccessProvider;

class RelevantProductAdapter extends AbstractAdapter
{

    /**
     * @var RelevantProductIndexAccessProvider
     */
    private $relevantProductIndex;

    public function __construct(RelevantProductIndexAccessProvider $relevantProductIndex)
    {
        $this->relevantProductIndex = $relevantProductIndex;
    }

    /**
     * Boosts products based on preferences of similar customers
     * @param array $query
     * @param float $weight
     * @param string $boostMode
     * @return array
     */
    public function addPersonalization(array $query, float $weight = 1, string $boostMode = "multiply"): array
    {
        $customerId = Factory::getInstance()->getEnvironment()->getCurrentUserId();
        $response = $this->relevantProductIndex->fetchSegments($customerId);

        $functions = [];

        foreach($response as $segment) {
            $segmentId = $segment['segmentId'];
            $segmentCount = $segment['segmentCount'];
            $functions[] = [
                'filter' => [
                    'match' => ['relations.segments' => $segmentId]],
                'weight' => $segmentCount * $weight
            ];
        }

        if(count($functions) == 0) {
            return $query;
        }

        $relevantProductQuery = [
            'function_score' => [
                'query' => $query,
                'functions' => $functions,
                'boost_mode' => $boostMode
            ]
        ];

        return $relevantProductQuery;
    }
}
