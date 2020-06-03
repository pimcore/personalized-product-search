<?php


namespace Pimcore\Bundle\PersonalizedSearchBundle\IndexAccessProvider;


use Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad\CustomerGroupAssignment;

interface CustomerGroupIndexAccessProviderInterface
{
    public function fetchCustomerGroups(): array;
    public function indexCustomerGroupAssignment(CustomerGroupAssignment $customerGroupAssignment);
    public function dropCustomerGroupAssignmentIndex();
    public function dropCustomerGroupIndex();
    public function createCustomerGroupAssignmentIndex();
    public function createCustomerGroupIndex();
}
