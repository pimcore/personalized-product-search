<?php

namespace Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad;


use PersonalizedSearchBundle\src\ExtractTransformLoad\CustomerGroupSegments;
use Pimcore\Bundle\PersonalizedSearchBundle\IndexAccessProvider\IndexAccessProviderInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\Factory;
use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\Getter\GetterInterface;
use Pimcore\Model\DataObject;

class CustomerGroupProvider implements CustomerGroupIndexAccessProviderInterface
{
    private $segmentGetter;
    private $customerGroupIndexAccessProvider;

    public function __construct(GetterInterface $getter, CustomerGroupIndexAccessProviderInterface $customerGroupIndexAccessProvider) {
        $this->segmentGetter = $getter;
        $this->$customerGroupIndexAccessProvider = $customerGroupIndexAccessProvider;
    }

    public function updateCustomerGroupAndSegmentsIndicesFromOrderDb()
    {
        $customers = new DataObject\Customer\Listing();

        $this->customerGroupIndexAccessProvider->dropCustomerGroupSegmentsIndex();
        $this->customerGroupIndexAccessProvider->dropCustomerGroupIndex();

        foreach($customers as $customer) {
            assignCustomerToCustomerGroup($customer->getId());
        }

    }

    private function assignCustomerToCustomerGroup(int $customerId) {
        $customers = new DataObject\Customer\Listing();
        $customerInfo = self::getPurchaseHistory($customerId);

        foreach($customers as $customer) {
            if($customerId == $customer->getId())
                continue;

            $currentCustomerInfo = self::getPurchaseHistory($customer->getId());
            $intersection = array_intersect($customerInfo->segments, $currentCustomerInfo->segments);

            if((sizeof($customerInfo->segments) * 0.6)  <= (sizeof($intersection))) {
                $this->createCustomerGroup($this->customerGroupIndexAccessProvider->fetchCustomerGroupWithSegments($customer->getId()), $customerId, $customer->getId());
                return;
            }
        }
    }

    private function createCustomerGroup($groupSegments, $customerId, $customerMatchedId)
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
            $customerGroup = new CustomerGroup($customerId, $this->customerGroupIndexAccessProvider->fetchCustomerGroupWithSegments($customerMatchedId));
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
