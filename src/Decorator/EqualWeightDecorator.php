<?php


namespace Pimcore\Bundle\PersonalizedSearchBundle\Decorator;


use Pimcore\Bundle\PersonalizedSearchBundle\Adapter\AdapterInterface;

class EqualWeightDecorator extends AbstractDecorator
{
    protected function invokeAdapter(AdapterInterface $adapter, array $query): array
    {
        return $adapter->addPersonalization($query);
    }
}
