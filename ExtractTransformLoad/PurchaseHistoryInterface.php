<?php


namespace PersonalizedSearchBundle\ExtractTransformLoad;

interface PurchaseHistoryInterface
{
    public function GetPurchaseHistory(int $customerId) : object;
    public function FillOrderIndex(object $customerInfo);
    public function UpdateOrderIndexFromOrderDb();
}
