<?php


namespace Pimcore\Bundle\PersonalizedSearchBundle\Adapter;

use Pimcore\Bundle\PersonalizedSearchBundle\Customer\PersonalizationAdapterCustomerIdProvider as CustomerIdProvider;
use Pimcore\Bundle\PersonalizedSearchBundle\IndexAccessProvider\RelevantProductIndexAccessProvider;

class RelevantProductsAdapter extends AbstractAdapter
{

    /**
     * @var RelevantProductIndexAccessProvider
     */
    private $relevantProductIndex;

    /**
     * @var CustomerIdProvider
     */
    private $customerIdProvider;

    public function __construct(RelevantProductIndexAccessProvider $relevantProductIndex, CustomerIdProvider $customerIdProvider)
    {
        $this->relevantProductIndex = $relevantProductIndex;
        $this->customerIdProvider = $customerIdProvider;
    }

    /**
     * Boosts products based on preferences of similar customers
     * @param array $query
     * @param float $weight
     * @param string $boostMode
     * @return array
     */
    public function addPersonalization(array $query, float $weight = 1.0, string $boostMode = "multiply"): array
    {
        $customerId = $this->customerIdProvider->getCustomerId();
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
