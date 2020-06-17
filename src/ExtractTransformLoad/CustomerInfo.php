<?php


namespace Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad;


class CustomerInfo
{
    public $customerId;
    public $segments;

    public function __construct($customerId, $segments = [])
    {
        $this->customerId = $customerId;
        $this->segments = $segments;
    }

    public static function __set_state($serializedRepresentation)
    {
        $instance = new CustomerInfo($serializedRepresentation["customerId"]);
        foreach ($serializedRepresentation["segments"] as $segment)
        {
            $instance->segments[] = $segment;
        }
        return $instance;
    }
}
