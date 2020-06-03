<?php


namespace Pimcore\Bundle\PersonalizedSearchBundle\IndexAccessProvider;


use Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad\CustomerGroup;
use Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad\CustomerGroupSegments;

interface CustomerGroupIndexAccessProviderInterface
{
    public function fetchCustomerGroupSegments(): array;
    public function indexCustomerGroup(CustomerGroup $customerGroup);
    public function dropCustomerGroupIndex();
    public function dropCustomerGroupSegmentsIndex();
    public function createCustomerGroupIndex();
    public function createCustomerGroupSegmentsIndex();
}
