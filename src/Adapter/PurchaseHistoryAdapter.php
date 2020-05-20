<?php


namespace Pimcore\Bundle\PersonalizedSearchBundle\Adapter;

use Pimcore\Bundle\PersonalizedSearchBundle\Customer\PersonalizationAdapterCustomerIdProvider;
use Pimcore\Bundle\PersonalizedSearchBundle\IndexAccessProvider\OrderIndexAccessProvider;

class PurchaseHistoryAdapter extends AbstractAdapter
{
    /**
     * @var OrderIndexAccessProvider
     */
    private $orderIndex;

    /**
     * @var PersonalizationAdapterCustomerIdProvider
     */
    private $customerIdProvider;

    public function __construct(OrderIndexAccessProvider $orderIndex, PersonalizationAdapterCustomerIdProvider $purchaseHistoryAdapterCustomerIdProvider)
    {
        $this->orderIndex = $orderIndex;
        $this->customerIdProvider = $purchaseHistoryAdapterCustomerIdProvider;
    }

    /**
     * Boosts accessory parts more than cars
     * WARNING: this is a not generic adapter and is specific to the demo app
     * @param array $query
     * @param float $weight
     * @param string $boostMode
     * @return array
     */
    public function addPersonalization(array $query, float $weight = 1.0, string $boostMode = "multiply"): array
    {
        $customerId = $this->customerIdProvider->getCustomerId();
        $response = $this->orderIndex->fetchSegments($customerId);

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

        $purchaseHistoryQuery = [
            'function_score' => [
                'query' => $query,
                'functions' => $functions,
                'boost_mode' => $boostMode
            ]
        ];

        return $purchaseHistoryQuery;
    }
}
