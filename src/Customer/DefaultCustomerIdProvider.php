<?php


namespace Pimcore\Bundle\PersonalizedSearchBundle\Customer;


use Pimcore\Model\DataObject\AbstractObject;
use Symfony\Component\Security\Core\Security;

class DefaultCustomerIdProvider implements PersonalizationAdapterCustomerIdProvider
{

    protected $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }


    public function getCustomerId(): int
    {
        $user = $this->security->getUser();
        if($user instanceof AbstractObject) {
            return $user->getId();
        }
        throw new \Exception("No User Logged In or User no Pimcore Object");
    }
}