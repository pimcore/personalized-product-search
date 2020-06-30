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
use Pimcore\Bundle\PersonalizedSearchBundle\Customer\PersonalizationAdapterCustomerIdProvider as CustomerIdProvider;
use Pimcore\Bundle\PersonalizedSearchBundle\IndexAccessProvider\CustomerGroupIndexAccessProviderInterface;

class RelevantProductsAdapter extends AbstractAdapter
{
    private static $ADDITIONAL_WEIGHT = 8;

    /**
     * @var CustomerGroupIndexAccessProviderInterface
     */
    private $relevantProductIndex;

    /**
     * @var CustomerIdProvider
     */
    private $customerIdProvider;

    public function __construct(CustomerGroupIndexAccessProviderInterface $relevantProductIndex, CustomerIdProvider $customerIdProvider, SegmentManagerInterface $segmentManager)
    {
        parent::__construct($segmentManager);
        $this->relevantProductIndex = $relevantProductIndex;
        $this->customerIdProvider = $customerIdProvider;
    }

    /**
     * Boosts products based on preferences of similar customers
     *
     * @param array $query
     * @param float $weight
     * @param string $boostMode
     *
     * @return array
     */
    public function addPersonalization(array $query, float $weight = 1.0, string $boostMode = 'multiply'): array
    {
        $customerId = $this->customerIdProvider->getCustomerId();
        $response = $this->relevantProductIndex->fetchSegments($customerId);

        $functions = [];

        foreach ($response as $segment) {
            $segmentId = $segment['segmentId'];
            $segmentCount = $segment['segmentCount'];
            $functions[] = [
                'filter' => [
                    'match' => ['relations.segments' => $segmentId]],
                'weight' => $segmentCount * $weight * self::$ADDITIONAL_WEIGHT
            ];
        }

        if (count($functions) == 0) {
            return $query;
        }

        $relevantProductQuery = [
            'function_score' => [
                'query' => $query,
                'functions' => $functions,
                'boost_mode' => $boostMode
            ]
        ];

        return $relevantProductQuery;
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
        $customerId = $this->customerIdProvider->getCustomerId();
        $response = $this->relevantProductIndex->fetchSegments($customerId);

        $info = [
            'adapter' => get_class($this),
            'boostMode' => $boostMode,
            'segments' => []
        ];

        foreach ($response as $segment) {
            $segmentId = $segment['segmentId'];
            $segmentCount = $segment['segmentCount'];
            $info['segments'][] = [
                'segmentId' => $segmentId,
                'segmentName' => $this->getSegmentName($segmentId),
                'weight' => $segmentCount * $weight
            ];
        }

        return $info;
    }
}
