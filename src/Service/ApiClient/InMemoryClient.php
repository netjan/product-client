<?php

namespace App\Service\ApiClient;

/**
 * @template T of object
 *
 * @template-implements ApiClientInterface<T>
 */
class InMemoryClient implements ApiClientInterface
{
    private ApiClientInterface $apiClient;

    public function __construct(ApiClientInterface $apiClient)
    {
        $this->apiClient = $apiClient;
    }
    public function find(mixed $id): ?object
    {
        return null;
    }

    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        return [];
    }

    public function save(object $object): void
    {
    }

    public function remove(object $object): void
    {
    }
}
