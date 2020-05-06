<?php


namespace Pimcore\Bundle\PersonalizedSearchBundle\Adapter;

use Pimcore\Bundle\PersonalizedSearchBundle\IndexAccessProvider\OrderIndexAccessProvider;
use Pimcore\Bundle\EcommerceFrameworkBundle\Factory;

class PurchaseHistoryAdapter extends AbstractAdapter
{
    public static $PURCHASE_WEIGHT_MULTIPLIER = 6;

    /**
     * @var OrderIndexAccessProvider
     */
    private $orderIndex;

    public function __construct(OrderIndexAccessProvider $orderIndex)
    {
        $this->orderIndex = $orderIndex;
    }

    /**
     * Boosts accessory parts more than cars
     * WARNING: this is a not generic adapter and is specific to the demo app
     * @param array $query
     * @param float $weight
     * @param string $boostMode
     * @return array
     */
    public function addPersonalization(array $query, float $weight = 1, string $boostMode = "multiply"): array
    {
        $customerId = Factory::getInstance()->getEnvironment()->getCurrentUserId();
        $response = $this->orderIndex->fetchSegments($customerId);

        $functions = [];

        foreach($response as $segment) {
            $segmentId = $segment['segmentId'];
            $segmentCount = $segment['segmentCount'];
            $functions[] = [
                'filter' => [
                    'match' => ['relations.segments' => $segmentId]],
                'weight' => $segmentCount * $weight * self::$PURCHASE_WEIGHT_MULTIPLIER
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
