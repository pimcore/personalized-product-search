<?php


namespace Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad;


use Pimcore\Bundle\EcommerceFrameworkBundle\OrderManager\OrderManagerInterface;

interface PersonalizationOrderManagerProvider
{
    function getOrderManager(): OrderManagerInterface;
    function getCustomerClassId(): string;
}
