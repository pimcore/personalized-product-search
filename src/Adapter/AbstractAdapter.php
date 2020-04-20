<?php


namespace Pimcore\Bundle\PersonalizedSearchBundle\Adapter;

class AbstractAdapter implements AdapterInterface
{
    public function addPersonalization(array $query, float $weight = 1, string $boostMode = "multiply"): array
    {
        // TODO: Implement addPersonalization() method.
    }
}
