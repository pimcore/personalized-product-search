<?php


namespace Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad;


class CustomerGroup
{
    public $customerId;
    public $customerGroupSegments;

    public function __construct($id, CustomerGroupSegments $customerGroupSegments)
    {
        $this->customerId = $id;
        $this->customerGroupSegments = $customerGroupSegments;
    }
}
