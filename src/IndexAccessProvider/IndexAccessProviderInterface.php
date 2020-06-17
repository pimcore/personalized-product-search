<?php

namespace Pimcore\Bundle\PersonalizedSearchBundle\IndexAccessProvider;

interface IndexAccessProviderInterface
{
    /**
     * Returns all purchase history segments of a customer
     * @param int $customerId Customer id / user id
     * @return array Segment array
     */
    public function fetchSegments(int $customerId): array;

    /**
     * Adds the document to the index with the specified ID
     * @param int $documentId
     * @param object $body
     * @return mixed
     */
    public function index(int $documentId, object $body);
}
