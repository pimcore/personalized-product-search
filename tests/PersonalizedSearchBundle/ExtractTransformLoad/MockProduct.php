<?php


namespace Pimcore\Bundle\PersonalizedSearchBundle\Tests\ExtractTransformLoad;


class MockProduct
{
    private $productNumber;

    public function __construct(string $productNumber)
    {
        $this->productNumber = $productNumber;
    }

    public function getProductNumber(): string
    {
        return $this->productNumber;
    }


}
