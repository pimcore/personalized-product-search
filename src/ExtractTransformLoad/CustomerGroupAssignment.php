<?php


namespace Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad;


class CustomerGroupAssignment
{
    public $customerId;
    public $customerGroup;

    public function __construct($id, CustomerGroup $customerGroup)
    {
        $this->customerId = $id;
        $this->customerGroup = $customerGroup;
    }
}
