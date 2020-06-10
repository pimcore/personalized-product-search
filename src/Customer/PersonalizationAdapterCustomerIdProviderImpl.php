<?php


namespace Pimcore\Bundle\PersonalizedSearchBundle\Customer;

use Pimcore\Bundle\EcommerceFrameworkBundle\Factory;

class PersonalizationAdapterCustomerIdProviderImpl implements PersonalizationAdapterCustomerIdProvider {

    public function getCustomerId(): int {
        return Factory::getInstance()->getEnvironment()->getCurrentUserId();
    }
}
