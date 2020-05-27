<?php


namespace Pimcore\Bundle\PersonalizedSearchBundle\Customer;


interface PersonalizationAdapterCustomerIdProvider
{
    public function getCustomerId(): int;
}
