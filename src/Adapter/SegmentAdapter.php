<?php


namespace Pimcore\Bundle\PersonalizedSearchBundle\Adapter;

use CustomerManagementFrameworkBundle\Targeting\SegmentTracker;
use Pimcore\Targeting\VisitorInfoStorage;

class SegmentAdapter extends AbstractAdapter
{
    /**
     * @var VisitorInfoStorage
     */
    private $visitorInfoStorage;

    /**
     * @var SegmentTracker
     */
    private $segmentTracker;

    /**
     * SegmentAdapter constructor.
     * @param VisitorInfoStorage $visitorInfoStorage
     * @param SegmentTracker $segmentTracker
     */
    function __construct(VisitorInfoStorage $visitorInfoStorage, SegmentTracker $segmentTracker) {
        $this->visitorInfoStorage = $visitorInfoStorage;
        $this->segmentTracker = $segmentTracker;
    }

    /**
     * Adds boosting based on user segments
     * @param array $query
     * @param float $weight
     * @param string $boostMode
     * @return array
     */
    public function addPersonalization(array $query, float $weight = 1.0, string $boostMode = "multiply"): array
    {
        $functions = [];

        $segments = $this->segmentTracker->getAssignments($this->visitorInfoStorage->getVisitorInfo());
        foreach ($segments as $segmentId => $count) {
            $functions[] = [
                'filter' => ['match' => ['relations.segments' => $segmentId]],
                'weight' => $count * $weight
            ];
        }

        $segmentQuery = [
            'function_score' => [
                'query' => $query,
                'functions' => $functions,
                'boost_mode' => $boostMode
            ]
        ];

        return $segmentQuery;
    }
}
