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

class OrderIndexAccessProvider extends EsAwareIndexAccessProvider implements IndexAccessProviderInterface
{
    /**
     * @var string
     */
    private static $indexName = 'order_segments';

    /**
     * Returns all purchase history segments of a customer
     *
     * @param int $customerId Customer id / user id
     *
     * @return array Segment array
     */
    public function fetchSegments(int $customerId): array
    {
        $params = [
            'index' => $this->indexPrefix . self::$indexName,
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

        return $response[0]['_source']['segments'];
    }

    /**
     * Adds the document to the index with the specified ID
     *
     * @param int $documentId
     * @param object $body
     *
     * @return mixed
     */
    public function index(int $documentId, object $body)
    {
        $params = [
            'index' => $this->indexPrefix . self::$indexName,
            'type' => '_doc',
            'id' => $documentId,
            'body' => $body
        ];
        $this->esClient->index($params);
    }
}
