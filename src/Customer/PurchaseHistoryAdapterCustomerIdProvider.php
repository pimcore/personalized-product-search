<?php


namespace Pimcore\Bundle\PersonalizedSearchBundle\Customer;


interface PurchaseHistoryAdapterCustomerIdProvider
{
    public function getCustomerId(): int;
}
