<?php


namespace Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad\SegmentExtractor;

/**
 * Provides a dummy implementation for product segment extraction and always returns an empty array
 */
class DummySegmentExtractor implements ProductSegmentExtractorInterface
{

    /**
     * @param $product
     * @return mixed
     */
    public function get($product)
    {
        return [];
    }
}