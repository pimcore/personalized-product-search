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
        $customers = new DataObject\Customer\Listing();
        $customerInfo = self::getPurchaseHistory($customerId);

        foreach ($customers as $customer)
        {
            if ($customerId == $customer->getId())
                continue;

            $currentCustomerInfo = self::getPurchaseHistory($customer->getId());
            $customerInfoSegmentIds = array_map(function ($entry) {
                return $entry->segmentId;
            }, $customerInfo->segments);
            $currentCustomerInfoSegmentIds = array_map(function ($entry) {
                return $entry->segmentId;
            }, $currentCustomerInfo->segments);
            $intersection = array_intersect($customerInfoSegmentIds, $currentCustomerInfoSegmentIds);

            //TODO: 3 fälle, neue gruppe anlegen, gruppe zuweisen, oder auslassen ( wenn segmente des customer leer sind)
            //TODO: intersection prozente in relation zur gesamtlänge beider customer und nicht nur von einem

            $createNewGroup = $this->handleIntersectionValue($intersection, $customerInfoSegmentIds, $currentCustomerInfoSegmentIds);

            if ((sizeof($customerInfo->segments) * 0.6) <= (sizeof($intersection)))
            {
                {
                    $this->createCustomerGroup($this->customerGroupIndexAccessProvider->fetchCustomerGroupWithSegments($customer->getId()), $customerId, $customer->getId(), $intersection);
                    return;
                }
            }
        }
    }

    private function handleIntersectionValue($intersection)
    {
        $returnValue = false;
        if(sizeof($intersection) === 0)
            $returnValue = true;

        //TODO: spezialfälle ergänzen, ob neue gruppe angelegt werden soll oder nicht

    }

    private function createCustomerGroup($groupSegments, $customerId, $customerMatchedId, $intersection)
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
