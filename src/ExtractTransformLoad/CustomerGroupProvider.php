<?php

namespace Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad;


use Pimcore\Bundle\EcommerceFrameworkBundle\Factory;
use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\Getter\GetterInterface;
use Pimcore\Bundle\PersonalizedSearchBundle\IndexAccessProvider\CustomerGroupIndexAccessProviderInterface;
use Pimcore\Model\DataObject;

class CustomerGroupProvider implements CustomerGroupInterface
{
    private $segmentGetter;
    private $customerGroupIndexAccessProvider;
    private const PROCENTUAL_INTERSECTION = 0.4;

    public function __construct(GetterInterface $getter, CustomerGroupIndexAccessProviderInterface $customerGroupIndexAccessProvider) {
        $this->segmentGetter = $getter;
        $this->customerGroupIndexAccessProvider = $customerGroupIndexAccessProvider;
    }

    public function updateCustomerGroupAndSegmentsIndicesFromOrderDb()
    {
        $customers = new DataObject\Customer\Listing();

        $this->customerGroupIndexAccessProvider->dropCustomerGroupSegmentsIndex();
        $this->customerGroupIndexAccessProvider->dropCustomerGroupIndex();

        $this->customerGroupIndexAccessProvider->createCustomerGroupIndex();
        $this->customerGroupIndexAccessProvider->createCustomerGroupSegmentsIndex();

        foreach($customers as $customer) {
            $this->assignCustomerToCustomerGroup($customer->getId());
        }

    }

    private function assignCustomerToCustomerGroup(int $customerId)
    {
        $customerHasGroup = false;
        $customers = new DataObject\Customer\Listing();
        $customerInfo = self::getPurchaseHistory($customerId);

        $customerInfoSegmentIds = array_map(function ($entry) {
            return $entry->segmentId;
        }, $customerInfo->segments);

        foreach ($customers as $customer)
        {
            if ($customerId == $customer->getId())
                continue;

            $currentCustomerInfo = self::getPurchaseHistory($customer->getId());

            $currentCustomerInfoSegmentIds = array_map(function ($entry) {
                return $entry->segmentId;
            }, $currentCustomerInfo->segments);
            $intersection = array_intersect($customerInfoSegmentIds, $currentCustomerInfoSegmentIds);

            $createNewGroup = $this->handleIntersectionValue($intersection, $customerInfoSegmentIds, $currentCustomerInfoSegmentIds);

            if ($createNewGroup)
            {
                $this->createCustomerGroup($this->customerGroupIndexAccessProvider->fetchCustomerGroupWithSegments($customer->getId()), $customerId, $intersection);
                $customerHasGroup = true;
                break;
            }
        }

        if(!$customerHasGroup && sizeof($customerInfoSegmentIds) > 0)
            $this->createCustomerGroup($this->customerGroupIndexAccessProvider->fetchCustomerGroupWithSegments($customerId), $customerId, $customerInfoSegmentIds);
    }

    private function handleIntersectionValue($intersection, $customerInfoSegmentIds, $currentCustomerInfoSegmentIds)
    {
        $createNewGroup = true;

        if(sizeof($intersection) === 0)
            $createNewGroup = false;
        if(((sizeof($customerInfoSegmentIds) * self::PROCENTUAL_INTERSECTION) > sizeof($intersection))
            || (sizeof($currentCustomerInfoSegmentIds) * self::PROCENTUAL_INTERSECTION) > sizeof($intersection))
            $createNewGroup = false;

        return $createNewGroup;
    }

    private function createCustomerGroup($groupSegments, $customerId, $intersection)
    {
        if($groupSegments == null) {
            $allCustomerGroups = $this->customerGroupIndexAccessProvider->fetchCustomerGroups();
            $newId = 1;
            if(sizeof($allCustomerGroups) > 0)
                $newId = max(array_map(function($customerGroups) { return $customerGroups->customerGroupId; }, $allCustomerGroups)) + 1;

            $customerGroup = new CustomerGroup($customerId, new CustomerGroupSegments($newId, $intersection));
            $this->customerGroupIndexAccessProvider->indexCustomerGroup($customerGroup);
        }
        else {
            $customerGroup = new CustomerGroup($customerId, $groupSegments);
            $this->customerGroupIndexAccessProvider->indexCustomerGroup($customerGroup);
        }
    }

    private function getPurchaseHistory(int $customerId): CustomerInfo
    {
        $orderManager = Factory::getInstance()->getOrderManager();

        $orderList =  $orderManager->createOrderList();
        $orderQuery = $orderList->getQuery();

        $orderList->joinCustomer(\Pimcore\Model\DataObject\Customer::classId());
        $orderQuery->where('customer.o_id = ?', $customerId);


        $customerInfo = new CustomerInfo($customerId);

        foreach($orderList as $order)
        {
            foreach($order->getItems() as $item)
            {
                $product = $item->getProduct();

                $segments = $this->segmentGetter->get($product);

                foreach ($segments as $segment)
                {
                    $segmentId = $segment->getId();
                    $found = false;
                    foreach($customerInfo->segments as $e)
                    {
                        if($e->segmentId === $segmentId)
                        {
                            $e->segmentCount++;
                            $found = true;
                        }
                    }

                    if(!$found){
                        $segmentInfo = new SegmentInfo($segmentId, 1);
                        $customerInfo->segments[] = $segmentInfo;
                    }
                }
            }
        }
        return $customerInfo;
    }
}
