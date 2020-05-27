<?php


namespace Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad;


class CustomerInfo
{
    public $customerId;
    public $segments;

    public function __construct($customerId)
    {
        $this->customerId = $customerId;
        $this->segments = [];
    }
}
