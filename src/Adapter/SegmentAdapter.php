<?php


namespace Pimcore\Bundle\PersonalizedSearchBundle\Adapter;

use CustomerManagementFrameworkBundle\Targeting\SegmentTracker;
use Pimcore\Targeting\VisitorInfoStorage;
use Psr\Log\LoggerInterface;

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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * SegmentAdapter constructor.
     * @param VisitorInfoStorage $visitorInfoStorage
     * @param SegmentTracker $segmentTracker
     * @param LoggerInterface $logger
     */
    function __construct(VisitorInfoStorage $visitorInfoStorage, SegmentTracker $segmentTracker, LoggerInterface $logger) {
        $this->visitorInfoStorage = $visitorInfoStorage;
        $this->segmentTracker = $segmentTracker;
        $this->logger = $logger;
    }

    /**
     * Adds boosting based on user segments
     * @param array $query
     * @param float $weight
     * @param string $boostMode
     * @return array
     */
    public function addPersonalization(array $query, float $weight = 1, string $boostMode = "multiply"): array
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
