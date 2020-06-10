<?php

namespace Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad;

interface CustomerGroupInterface
{
    public function updateCustomerGroupAndSegmentsIndicesFromOrderDb();
}
