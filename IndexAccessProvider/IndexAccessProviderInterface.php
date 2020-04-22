<?php

namespace PersonalizedSearchBundle\IndexAccessProvider;

interface IndexAccessProviderInterface
{
    public function fetchSegments(int $customerId): array;
    public function index(int $documentId, array $body);
}
