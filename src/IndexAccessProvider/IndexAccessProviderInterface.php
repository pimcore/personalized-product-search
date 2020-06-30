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

interface IndexAccessProviderInterface
{
    /**
     * Returns all purchase history segments of a customer
     *
     * @param int $customerId Customer id / user id
     *
     * @return array Segment array
     */
    public function fetchSegments(int $customerId): array;

    /**
     * Adds the document to the index with the specified ID
     *
     * @param int $documentId
     * @param object $body
     *
     * @return mixed
     */
    public function index(int $documentId, object $body);
}
