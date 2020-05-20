<?php


namespace Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad;

interface PurchaseHistoryInterface
{
    public function fillOrderIndex(CustomerInfo $customerInfo);
    public function updateOrderIndexFromOrderDb();
}
