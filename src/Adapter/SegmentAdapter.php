<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace Pimcore\Bundle\PersonalizedSearchBundle\Adapter;

use CustomerManagementFrameworkBundle\SegmentManager\SegmentManagerInterface;
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
     *
     * @param VisitorInfoStorage $visitorInfoStorage
     * @param SegmentTracker $segmentTracker
     * @param SegmentManagerInterface $segmentManager
     */
    public function __construct(VisitorInfoStorage $visitorInfoStorage, SegmentTracker $segmentTracker, SegmentManagerInterface $segmentManager)
    {
        parent::__construct($segmentManager);
        $this->visitorInfoStorage = $visitorInfoStorage;
        $this->segmentTracker = $segmentTracker;
    }

    /**
     * Adds boosting based on user segments
     *
     * @param array $query
     * @param float $weight
     * @param string $boostMode
     *
     * @return array
     */
    public function addPersonalization(array $query, float $weight = 1.0, string $boostMode = 'multiply'): array
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

    /**
     * Get boosting values
     *
     * @param float $weight
     * @param string $boostMode
     *
     * @return array
     */
    public function getDebugInfo(float $weight = 1.0, string $boostMode = 'multiply'): array
    {
        $info = [
            'adapter' => get_class($this),
            'boostMode' => $boostMode,
            'segments' => []
        ];

        $segments = $this->segmentTracker->getAssignments($this->visitorInfoStorage->getVisitorInfo());
        foreach ($segments as $segmentId => $count) {
            $info['segments'][] = [
                'segmentId' => $segmentId,
                'segmentName' => $this->getSegmentName($segmentId),
                'weight' => $count * $weight
            ];
        }

        return $info;
    }
}
