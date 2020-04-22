<?php


namespace Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad;

interface PurchaseHistoryInterface
{
    public function getPurchaseHistory(int $customerId) : object;
    public function fillOrderIndex(object $customerInfo);
    public function updateOrderIndexFromOrderDb();
}
