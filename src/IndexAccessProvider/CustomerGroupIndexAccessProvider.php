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

namespace Pimcore\Bundle\PersonalizedSearchBundle\IndexAccessProvider;

use Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad\CustomerGroup;
use Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad\CustomerGroupAssignment;
use Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad\SegmentInfo;

class CustomerGroupIndexAccessProvider extends EsAwareIndexAccessProvider implements CustomerGroupIndexAccessProviderInterface
{
    /**
     * @var string
     */
    private static $customerGroupAssignmentIndex = 'customergroup';

    /**
     * @var string
     */
    private static $customerGroupIndex = 'customergroup_segments';

    private function indexByName(int $documentId, object $body, string $indexName)
    {
        $params = [
            'index' => $this->indexPrefix . $indexName,
            'type' => '_doc',
            'id' => $documentId,
            'body' => $body
        ];
        $this->esClient->index($params);
    }

    public function fetchCustomerGroups(): array
    {
        $params = [
            'index' => $this->indexPrefix . self::$customerGroupIndex,
            'type' => '_doc',
            'body' => [
                'query' => [
                    'match_all' => (object) []
                ]
            ]
        ];
        $response = $this->esClient->search($params)['hits']['hits'];

        $customerGroupSegments = [];

        foreach ($response as $value) {
            $segmentInfos = array_map(function ($entry) {
                return new SegmentInfo($entry['segmentId'], $entry['segmentCount']);
            }, $value['_source']['segments']);

            $customerGroupSegments[] = new CustomerGroup($value['_source']['customerGroupId'], $segmentInfos);
        }

        return $customerGroupSegments;
    }

    public function indexCustomerGroupAssignment(CustomerGroupAssignment $customerGroupAssignment)
    {
        $obj = new \stdClass();
        $obj->customerId = $customerGroupAssignment->customerId;
        $obj->customerGroupId = $customerGroupAssignment->customerGroup->customerGroupId;
        if (!$this->customerGroupExists($customerGroupAssignment->customerGroup->customerGroupId)) {
            // create new group if it doesn't exist already
            self::indexByName($customerGroupAssignment->customerGroup->customerGroupId, $customerGroupAssignment->customerGroup, self::$customerGroupIndex);
        }
        self::indexByName($customerGroupAssignment->customerId, $obj, self::$customerGroupAssignmentIndex);
    }

    private function customerGroupExists($customerGroupId)
    {
        $params = [
            'index' => $this->indexPrefix . self::$customerGroupIndex,
            'type' => '_doc',
            'body' => [
                'query' => [
                    'match' => [
                        'customerGroupId' => $customerGroupId
                    ]
                ]
            ]
        ];

        $response = $this->esClient->search($params)['hits']['hits'];

        return sizeof($response) === 0 ? false : true;
    }

    public function fetchSegments(int $customerId): array
    {
        $params = [
            'index' => $this->indexPrefix . self::$customerGroupAssignmentIndex,
            'type' => '_doc',
            'body' => [
                'query' => [
                    'match' => [
                        'customerId' => $customerId
                    ]
                ]
            ]
        ];

        $response = $this->esClient->search($params)['hits']['hits'];

        if (sizeof($response) === 0) {
            return [];
        }

        $customerGroupId = $response[0]['_source']['customerGroupId'];

        $params = [
            'index' => $this->indexPrefix . self::$customerGroupIndex,
            'type' => '_doc',
            'body' => [
                'query' => [
                    'match' => [
                        'customerGroupId' => $customerGroupId
                    ]
                ]
            ]
        ];

        $response = $this->esClient->search($params)['hits']['hits'];

        if (sizeof($response) === 0) {
            return [];
        }

        return $response[0]['_source']['segments'];
    }

    public function dropCustomerGroupAssignmentIndex()
    {
        if ($this->esClient->indices()->exists(['index' => $this->indexPrefix . self::$customerGroupAssignmentIndex])) {
            $this->esClient->indices()->delete(['index' => $this->indexPrefix . self::$customerGroupAssignmentIndex]);
        }
    }

    public function dropCustomerGroupIndex()
    {
        if ($this->esClient->indices()->exists(['index' => $this->indexPrefix . self::$customerGroupIndex])) {
            $this->esClient->indices()->delete(['index' => $this->indexPrefix . self::$customerGroupIndex]);
        }
    }

    public function createCustomerGroupAssignmentIndex()
    {
        if (!$this->esClient->indices()->exists(['index' => $this->indexPrefix . self::$customerGroupAssignmentIndex])) {
            $this->esClient->indices()->create(['index' => $this->indexPrefix . self::$customerGroupAssignmentIndex]);
        }
    }

    public function createCustomerGroupIndex()
    {
        if (!$this->esClient->indices()->exists(['index' => $this->indexPrefix . self::$customerGroupIndex])) {
            $this->esClient->indices()->create(['index' => $this->indexPrefix . self::$customerGroupIndex]);
        }
    }
}
