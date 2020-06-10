<?php


namespace Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad;

use Pimcore\Bundle\EcommerceFrameworkBundle\Factory;
use Pimcore\Bundle\EcommerceFrameworkBundle\OrderManager\OrderManagerInterface;

class PersonalizationOrderManagerProviderImpl implements PersonalizationOrderManagerProvider {

    function getOrderManager(): OrderManagerInterface {
        return Factory::getInstance()->getOrderManager();
    }

    function getCustomerClassId(): string {
        return \Pimcore\Model\DataObject\Customer::classId();
    }
}
