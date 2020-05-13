<?php

namespace Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad;

use Pimcore\Bundle\EcommerceFrameworkBundle\Factory;
use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\Getter\GetterInterface;
use Pimcore\Bundle\PersonalizedSearchBundle\IndexAccessProvider\OrderIndexAccessProvider;
use Pimcore\Model\DataObject;

class PurchaseHistoryProvider implements PurchaseHistoryInterface
{
    private $segmentGetter;
    private $orderIndexAccessProvider;

    public function __construct(GetterInterface $getter, OrderIndexAccessProvider $orderIndexAccessProvider) {
        $this->segmentGetter = $getter;
        $this->orderIndexAccessProvider = $orderIndexAccessProvider;
    }

    public function updateOrderIndexFromOrderDb() {
        $customers = new DataObject\Customer\Listing();

        foreach($customers as $customer) {
            $customerInfo = self::getPurchaseHistory($customer->getId());
            $this->fillOrderIndex($customerInfo);
        }
    }

    public function fillOrderIndex(CustomerInfo $customerInfo) {
        $this->orderIndexAccessProvider->index($customerInfo->customerId, $customerInfo);
    }

    public function getPurchaseHistory(int $customerId): CustomerInfo
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
