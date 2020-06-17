<?php


namespace Pimcore\Bundle\PersonalizedSearchBundle\Tests\ExtractTransformLoad;


class MockOrderItem
{
    private $product;

    public function __construct(MockProduct $product)
    {
        $this->product = $product;
    }

    public function getProduct(): MockProduct
    {
        return $this->product;
    }
}
