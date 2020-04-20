<?php


namespace Pimcore\Bundle\PersonalizedSearchBundle\Adapter;

interface AdapterInterface
{
    public function addPersonalization(array $query, float $weight = 1, string $boostMode = "multiply"): array;
}
