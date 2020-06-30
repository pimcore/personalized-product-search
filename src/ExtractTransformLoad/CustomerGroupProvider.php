<?php

namespace Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad;


use Pimcore\Bundle\EcommerceFrameworkBundle\Factory;
use Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad\SegmentExtractor\ProductSegmentExtractorInterface;
use Pimcore\Bundle\PersonalizedSearchBundle\IndexAccessProvider\CustomerGroupIndexAccessProviderInterface;
use Pimcore\Model\DataObject;

class CustomerGroupProvider implements CustomerGroupInterface
{
    /**
     * @var ProductSegmentExtractorInterface
     */
    private $segmentExtractor;

    /**
     * @var CustomerGroupIndexAccessProviderInterface
     */
    private $customerGroupIndexAccessProvider;

    private const PROCENTUAL_INTERSECTION = 0.6;

    public function __construct(ProductSegmentExtractorInterface $segmentExtractor, CustomerGroupIndexAccessProviderInterface $customerGroupIndexAccessProvider) {
        $this->segmentExtractor = $segmentExtractor;
        $this->customerGroupIndexAccessProvider = $customerGroupIndexAccessProvider;
    }

    /**
     * Creates and updates the customer group and customer group assignment indices
     */
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

    /**
     * Assigns a customer to a group of similar customers
     * If no group is found, a new one is created
     * @param int $customerId
     */
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

            if (sizeof($customerInfoSegmentIds) > 0 && sizeof($customerGroupSegmentIds) > 0) {

                $customerInfoMatchPercentage = sizeof($intersection) / sizeof($customerInfoSegmentIds);
                $customerGroupMatchPercentage = sizeof($intersection) / sizeof($customerGroupSegmentIds);

                if ($customerInfoMatchPercentage > self::PROCENTUAL_INTERSECTION
                    && $customerGroupMatchPercentage > self::PROCENTUAL_INTERSECTION)
                {
                    // assign to existing group
                    $customerGroupAssignment = new CustomerGroupAssignment($customerId, $customerGroup);
                    $this->customerGroupIndexAccessProvider->indexCustomerGroupAssignment($customerGroupAssignment);

                    $isAssigned = true;
                }
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

    /**
     * Gets the purchase history of a customer
     * @param int $customerId
     * @return CustomerInfo
     */
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

                $segments = $this->segmentExtractor->get($product);

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
