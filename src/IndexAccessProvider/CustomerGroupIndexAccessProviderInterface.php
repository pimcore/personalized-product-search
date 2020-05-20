<?php


namespace PersonalizedSearchBundle\src\IndexAccessProvider;


use PersonalizedSearchBundle\src\ExtractTransformLoad\CustomerGroupSegments;
use Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad\CustomerGroup;

interface CustomerGroupIndexAccessProviderInterface extends IndexAccessProviderInterface
{
    public function fetchCustomerGroupWithSegments($customerId) : CustomerGroupSegments;
    public function fetchCustomerGroups() : array;
    public function fetchCustomerGroupAssignments(): array;
    public function fetchCustomerGroupForCustomer($customerId): int;
    public function indexCustomerGroup(CustomerGroup $customerGroup);
    public function dropCustomerGroupIndex();
    public function dropCustomerGroupSegmentsIndex();
}
