<?php

namespace Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad;

use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\Getter\GetterInterface;
use Pimcore\Bundle\PersonalizedSearchBundle\IndexAccessProvider\IndexAccessProviderInterface;
use Pimcore\Bundle\PersonalizedSearchBundle\IndexAccessProvider\OrderIndexAccessProvider;
use Pimcore\Model\DataObject;

class PurchaseHistoryProvider implements PurchaseHistoryInterface
{
    private $segmentGetter;
    private $orderManagerProvider;
    private $orderIndexAccessProvider;

    public function __construct(GetterInterface $getter, PersonalizationOrderManagerProvider $orderManagerProvider, OrderIndexAccessProvider $orderIndexAccessProvider) {
        $this->segmentGetter = $getter;
        $this->orderManagerProvider = $orderManagerProvider;
        $this->orderIndexAccessProvider = $orderIndexAccessProvider;
    }

    public function updateOrderIndexFromOrderDb() {
        $customers = new DataObject\Customer\Listing();

        foreach($customers as $customer) {
            $customerInfo = $this->getPurchaseHistory($customer->getId());
            $this->fillOrderIndex($customerInfo);
        }
    }

    public function fillOrderIndex(CustomerInfo $customerInfo) {
        $this->orderIndexAccessProvider->index($customerInfo->customerId, $customerInfo);
    }

    public function getPurchaseHistory(int $customerId): CustomerInfo
    {
        $orderManager = $this->orderManagerProvider->getOrderManager();
        $orderList =  $orderManager->createOrderList();
        $orderQuery = $orderList->getQuery();

        $orderList->joinCustomer($this->orderManagerProvider->getCustomerClassId());
        $orderQuery->where('customer.o_id = ?', $customerId);

        $customerInfo = new CustomerInfo($customerId);

        foreach ($orderList as $order)
        {
            foreach ($order->getItems() as $item)
            {
                $product = $item->getProduct();

                $segments = $this->segmentGetter->get($product);

                foreach ($segments as $segment)
                {
                    $segmentId = $segment->getId();
                    $found = false;
                    foreach ($customerInfo->segments as $element)
                    {
                        if ($element->segmentId === $segmentId)
                        {
                            $element->segmentCount++;
                            $found = true;
                        }
                    }

                    if(!$found)
                    {
                        $segmentInfo = new SegmentInfo($segmentId, 1);
                        $customerInfo->segments[] = $segmentInfo;
                    }
                }
            }
        }
        return $customerInfo;
    }
}
