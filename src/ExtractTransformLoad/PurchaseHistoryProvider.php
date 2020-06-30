<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad;

use Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad\SegmentExtractor\ProductSegmentExtractorInterface;
use Pimcore\Bundle\PersonalizedSearchBundle\IndexAccessProvider\OrderIndexAccessProvider;
use Pimcore\Model\DataObject;

class PurchaseHistoryProvider implements PurchaseHistoryInterface
{
    /**
     * @var ProductSegmentExtractorInterface
     */
    private $segmentExtractor;

    /**
     * @var PersonalizationOrderManagerProvider
     */
    private $orderManagerProvider;

    /**
     * @var OrderIndexAccessProvider
     */
    private $orderIndexAccessProvider;

    public function __construct(ProductSegmentExtractorInterface $segmentExtractor, PersonalizationOrderManagerProvider $orderManagerProvider, OrderIndexAccessProvider $orderIndexAccessProvider)
    {
        $this->segmentExtractor = $segmentExtractor;
        $this->orderManagerProvider = $orderManagerProvider;
        $this->orderIndexAccessProvider = $orderIndexAccessProvider;
    }

    /**
     * Creates and updates the order history index
     */
    public function updateOrderIndexFromOrderDb()
    {
        $customers = new DataObject\Customer\Listing();

        foreach ($customers as $customer) {
            $customerInfo = $this->getPurchaseHistory($customer->getId());
            $this->fillOrderIndex($customerInfo);
        }
    }

    /**
     * Adds a CustomerInfo object to the order history index
     *
     * @param CustomerInfo $customerInfo
     *
     * @return mixed
     */
    public function fillOrderIndex(CustomerInfo $customerInfo)
    {
        $this->orderIndexAccessProvider->index($customerInfo->customerId, $customerInfo);
    }

    /**
     * Returns the CustomerInfo with the purchase history for a customer
     *
     * @param int $customerId
     *
     * @return CustomerInfo
     */
    public function getPurchaseHistory(int $customerId): CustomerInfo
    {
        $orderManager = $this->orderManagerProvider->getOrderManager();
        $orderList = $orderManager->createOrderList();
        $orderQuery = $orderList->getQuery();

        $orderList->joinCustomer($this->orderManagerProvider->getCustomerClassId());
        $orderQuery->where('customer.o_id = ?', $customerId);

        $customerInfo = new CustomerInfo($customerId);

        foreach ($orderList as $order) {
            foreach ($order->getItems() as $item) {
                $product = $item->getProduct();

                $segments = $this->segmentExtractor->get($product);

                foreach ($segments as $segment) {
                    $segmentId = $segment->getId();
                    $found = false;
                    foreach ($customerInfo->segments as $element) {
                        if ($element->segmentId === $segmentId) {
                            $element->segmentCount++;
                            $found = true;
                        }
                    }

                    if (!$found) {
                        $segmentInfo = new SegmentInfo($segmentId, 1);
                        $customerInfo->segments[] = $segmentInfo;
                    }
                }
            }
        }

        return $customerInfo;
    }
}
