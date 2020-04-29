<?php

namespace Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad;

use Pimcore\Bundle\EcommerceFrameworkBundle\Factory;
use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\Getter\GetterInterface;
use Pimcore\Model\DataObject;
use Elasticsearch\ClientBuilder;

class PurchaseHistoryProvider implements PurchaseHistoryInterface
{
    private $segmentGetter;

    public function __construct(GetterInterface $getter) {
        $this->segmentGetter = $getter;
    }

    public function updateOrderIndexFromOrderDb() {
        $customers = new DataObject\Customer\Listing();

        foreach($customers as $customer) {
            $customerInfo = self::getPurchaseHistory($customer->getId());
            self::fillOrderIndex($customerInfo);
        }
    }

    public function fillOrderIndex(object $customerInfo) {
        $client = ClientBuilder::create()->build();

        $params = [
            'index' => 'order_segments',
            'type' => '_doc',
            'id' => $customerInfo->customerId,
            'body' => $customerInfo
        ];
        $client->index($params);
    }

    public function getPurchaseHistory(int $customerId): object
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

class CustomerInfo {
    public $customerId;
    public $segments;

    public function __construct($customerId) {
        $this->customerId = $customerId;
        $this->segments = [];
    }
};

class SegmentInfo {

    public $segmentId;
    public $segmentCount;

    public function __construct($segmentId, $segmentCount)
    {
        $this->segmentId = $segmentId;
        $this->segmentCount = $segmentCount;
    }
}
