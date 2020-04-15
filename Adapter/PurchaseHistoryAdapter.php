<?php


namespace PersonalizedSearchBundle\Adapter;

use PersonalizedSearchBundle\IndexAccessProvider\OrderIndexAccessProvider;
use Pimcore\Bundle\EcommerceFrameworkBundle\Factory;

class PurchaseHistoryAdapter extends AbstractAdapter
{
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
        $response = $this->orderIndex->getSegments($customerId);

        $functions = [];

        foreach($response as $segment) {
            $segmentId = $segment['segmentId'];
            $segmentCount = $segment['segmentCount'];
            // echo 'Segment Id: ' . $segmentId . ', Segment Count: ' . $segmentCount . '<br>';
            $functions[] = [
                'filter' => [
                    'match' => ['relations.segments' => $segmentId]],
                'weight' => $segmentCount * 6
            ];
        }

        $purchaseHistoryQuery = [
            'function_score' => [
                'query' => $query,
                'functions' => $functions,
                'boost_mode' => $boostMode
            ]
        ];

        // print_r($purchaseHistoryQuery);

        return $purchaseHistoryQuery;
    }
}
