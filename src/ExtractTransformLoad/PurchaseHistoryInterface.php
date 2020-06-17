<?php


namespace Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad;

interface PurchaseHistoryInterface
{
    /**
     * Adds a CustomerInfo object to the order history index
     * @param CustomerInfo $customerInfo
     * @return mixed
     */
    public function fillOrderIndex(CustomerInfo $customerInfo);

    /**
     * Creates and updates the order history index
     */
    public function updateOrderIndexFromOrderDb();

    /**
     * Returns the CustomerInfo with the purchase history for a customer
     * @param int $customerId
     * @return CustomerInfo
     */
    public function getPurchaseHistory(int $customerId): CustomerInfo;
}
