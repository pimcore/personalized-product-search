<?php


namespace PersonalizedSearchBundle\src\ExtractTransformLoad;


class CustomerGroupSegments
{
    public $customerGroupId;
    public $segments;

    public function __construct($id, $segments)
    {
        $this->customerGroupId = $id;
        $this->segments = $segments;
    }
}
