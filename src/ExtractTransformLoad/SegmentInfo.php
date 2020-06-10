<?php


namespace Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad;


class SegmentInfo
{
    public $segmentId;
    public $segmentCount;

    public function __construct($segmentId, $segmentCount)
    {
        $this->segmentId = $segmentId;
        $this->segmentCount = $segmentCount;
    }

    public static function __set_state($serializedRepresentation)
    {
        return new SegmentInfo(
            $serializedRepresentation["segmentId"],
            $serializedRepresentation["segmentCount"]
        );
    }
}
