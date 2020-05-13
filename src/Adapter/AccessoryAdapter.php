<?php


namespace Pimcore\Bundle\PersonalizedSearchBundle\Adapter;


class AccessoryAdapter extends AbstractAdapter
{
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
        $functions = [
            [
                'filter' => ['match' => ['system.o_classId' => 'AP']],
                'weight' => 1000
            ],
            [
                'filter' => ['match' => ['system.o_classId' => 'CAR']],
                'weight' => 1
            ]
        ];

        $accessoryQuery = [
            'function_score' => [
                'query' => $query,
                'functions' => $functions,
                'boost_mode' => $boostMode
            ]
        ];

        return $accessoryQuery;
    }
}
