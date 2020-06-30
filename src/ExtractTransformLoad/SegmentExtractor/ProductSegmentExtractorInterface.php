<?php


namespace Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad\SegmentExtractor;

interface ProductSegmentExtractorInterface
{
    /**
     * @param $product
     * @return mixed
     */
    public function get($product);
}