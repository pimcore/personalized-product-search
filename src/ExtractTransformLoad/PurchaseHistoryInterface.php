<?php


namespace Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad;

interface PurchaseHistoryInterface
{
    public function getPurchaseHistory(int $customerId) : CustomerInfo;
    public function fillOrderIndex(CustomerInfo $customerInfo);
    public function updateOrderIndexFromOrderDb();
}
