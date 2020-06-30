<?php


namespace Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad;


use Pimcore\Bundle\EcommerceFrameworkBundle\Factory;
use Pimcore\Bundle\EcommerceFrameworkBundle\OrderManager\OrderManagerInterface;
use Pimcore\Model\DataObject\ClassDefinition;

class DefaultOrderManagerProvider implements PersonalizationOrderManagerProvider
{

    /**
     * @var Factory
     */
    protected $factory;

    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    function getOrderManager(): OrderManagerInterface
    {
        return $this->factory->getOrderManager();
    }

    function getCustomerClassId(): string
    {
        $classDefinition = ClassDefinition::getByName('Customer');
        if($classDefinition) {
            return $classDefinition->getId();
        }

        throw new \Exception("No class definition with name 'customer' found.");
    }
}