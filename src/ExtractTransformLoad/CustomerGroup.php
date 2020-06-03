<?php


namespace Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad;


class CustomerGroup
{
    public $customerGroupId;
    public $segments;

    public function __construct(int $id, array $segmentInfos)
    {
        $this->customerGroupId = $id;
        $this->segments = $segmentInfos;
    }
}
