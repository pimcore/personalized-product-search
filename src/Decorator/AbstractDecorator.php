<?php

namespace Pimcore\Bundle\PersonalizedSearchBundle\Decorator;

use Pimcore\Bundle\PersonalizedSearchBundle\Adapter\AdapterInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\ProductListInterface;

abstract class AbstractDecorator implements AdapterInterface
{
    private $adapters;

    public function __construct(array $adapters = array())
    {
        $this->adapters = $adapters;
    }

    public function addAdapter(AdapterInterface $adapter): AbstractDecorator
    {
        $this->adapters[] = $adapter;
        return $this;
    }

    public function addPersonalization(array $query, float $weight = 1.0, string $boostMode = "multiply"): array
    {
        foreach ($this->adapters as $adapter) {
            $query = $this->invokeAdapter($adapter, $query);
        }
        return $query;
    }

    public function getDebugInfo(float $weight = 1.0, string $boostMode = "multiply"): array
    {
        foreach ($this->adapters as $adapter) {
            $res = $adapter->addDebugInfo($weight, $boostMode);
        }
        return $res;
    }

    abstract protected function invokeAdapter(AdapterInterface $adapter, array $query): array;
}
