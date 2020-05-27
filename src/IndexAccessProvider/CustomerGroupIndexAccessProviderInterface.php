<?php


namespace Pimcore\Bundle\PersonalizedSearchBundle\IndexAccessProvider;


use Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad\CustomerGroup;
use Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad\CustomerGroupSegments;

interface CustomerGroupIndexAccessProviderInterface
{
    public function fetchCustomerGroupWithSegments($customerId) : CustomerGroupSegments;
    public function fetchCustomerGroups() : array;
    public function fetchCustomerGroupAssignments(): array;
    public function fetchCustomerGroupForCustomer($customerId): int;
    public function indexCustomerGroup(CustomerGroup $customerGroup);
    public function dropCustomerGroupIndex();
    public function dropCustomerGroupSegmentsIndex();
}
