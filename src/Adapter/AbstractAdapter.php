<?php


namespace Pimcore\Bundle\PersonalizedSearchBundle\Adapter;

use CustomerManagementFrameworkBundle\SegmentManager\SegmentManagerInterface;

abstract class AbstractAdapter implements AdapterInterface
{

    /**
     * @var SegmentManagerInterface
     */
    protected $segmentManager;

    public function __construct(SegmentManagerInterface $segmentManager) {
        $this->segmentManager = $segmentManager;
    }

    protected function getSegmentName(int $segmentId): string {

        $segment = $this->segmentManager->getSegmentById($segmentId);
        if($segment) {
            return $segment->getName();
        }

        return '';
    }


}
