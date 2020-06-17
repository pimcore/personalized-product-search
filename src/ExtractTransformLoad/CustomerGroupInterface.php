<?php

namespace Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad;

interface CustomerGroupInterface
{
    /**
     * Creates and updates the customer group and customer group assignment indices
     */
    public function updateCustomerGroupAndSegmentsIndicesFromOrderDb();
}
