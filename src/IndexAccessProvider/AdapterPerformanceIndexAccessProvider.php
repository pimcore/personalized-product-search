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

class AdapterPerformanceIndexAccessProvider extends EsAwareIndexAccessProvider implements IndexAccessProviderInterface
{
    /**
     * @var string
     */
    private static $indexName = 'adapter_performance';

    public function fetchSegments(int $customerId): array
    {
        return [];
    }

    public function index(int $documentId, object $body)
    {
        $params = [
            'index' => $this->indexPrefix . self::$indexName,
            'type' => '_doc',
            'body' => $body
        ];
        $this->esClient->index($params);
    }
}
