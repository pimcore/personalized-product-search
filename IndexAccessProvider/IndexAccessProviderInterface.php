<?php

namespace PersonalizedSearchBundle\IndexAccessProvider;

interface IndexAccessProviderInterface
{
    public function getSegments(int $customerId): array;
    public function index(int $documentId, array $body);
}
