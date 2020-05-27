<?php


namespace Pimcore\Bundle\PersonalizedSearchBundle\Tests\ExtractTransformLoad;


use Pimcore\Bundle\EcommerceFrameworkBundle\OrderManager\OrderManagerInterface;
use Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad\PersonalizationOrderManagerProvider;


class MockOrderManagerProvider implements PersonalizationOrderManagerProvider
{
    private $orderManager;

    public function __construct(OrderManagerInterface $orderManager)
    {
        $this->orderManager = $orderManager;
    }

    function getOrderManager(): OrderManagerInterface
    {
        return $this->orderManager;
    }
}
