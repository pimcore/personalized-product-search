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

        $this->customerGroupIndexAccessProvider->dropCustomerGroupIndex();
        $this->customerGroupIndexAccessProvider->dropCustomerGroupAssignmentIndex();

        $this->customerGroupIndexAccessProvider->createCustomerGroupAssignmentIndex();
        $this->customerGroupIndexAccessProvider->createCustomerGroupIndex();

        foreach($customers as $customer) {
            $this->assignCustomerToCustomerGroup($customer->getId());
        }
    }

    private function assignCustomerToCustomerGroup(int $customerId)
    {
        $customerInfo = self::getPurchaseHistory($customerId);

        $customerInfoSegmentIds = array_map(function ($entry) {
            return $entry->segmentId;
        }, $customerInfo->segments);

        $allCustomerGroups = $this->customerGroupIndexAccessProvider->fetchCustomerGroups();
        $isAssigned = false;

        foreach ($allCustomerGroups as $customerGroup) {
            $customerGroupSegmentIds = array_map(function ($entry) {
                return $entry->segmentId;
            }, $customerGroup->segments);

            $intersection = array_intersect($customerInfoSegmentIds, $customerGroupSegmentIds);

            if (sizeof($customerInfoSegmentIds) > 0 && sizeof($customerGroupSegmentIds) > 0 &&
                sizeof($intersection) / sizeof($customerInfoSegmentIds) > self::PROCENTUAL_INTERSECTION
                && sizeof($intersection) / sizeof($customerGroupSegmentIds) > self::PROCENTUAL_INTERSECTION)
            {
                // assign to existing group
                $customerGroupAssignment = new CustomerGroupAssignment($customerId, $customerGroup);
                $this->customerGroupIndexAccessProvider->indexCustomerGroupAssignment($customerGroupAssignment);

                $isAssigned = true;
            }
        }

        if (!$isAssigned && sizeof($customerInfoSegmentIds) > 0) {
            // create new group
            $newId = 1;
            if(sizeof($allCustomerGroups) > 0) {
                $newId = max(array_map(function ($customerGroups) {
                        return $customerGroups->customerGroupId;
                    }, $allCustomerGroups)) + 1;
            }

            $customerGroupAssignment = new CustomerGroupAssignment($customerId, new CustomerGroup($newId, $customerInfo->segments));
            $this->customerGroupIndexAccessProvider->indexCustomerGroupAssignment($customerGroupAssignment);
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
