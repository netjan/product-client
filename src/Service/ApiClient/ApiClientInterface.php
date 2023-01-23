<?php

namespace App\Service\ApiClient;

use UnexpectedValueException;

/**
 * @template-covariant T of object
 */
interface ApiClientInterface
{
    /**
     * Finds an object by its primary key / identifier.
     *
     * @param mixed $id the identifier
     *
     * @return object|null the object
     *
     * @psalm-return T|null
     */
    public function find(mixed $id): ?object;

    /**
     * @param array<string, mixed>       $criteria
     * @param array<string, string>|null $orderBy
     *
     * @psalm-param array<string, 'asc'|'desc'|'ASC'|'DESC'>|null $orderBy
     *
     * @return array<int, object> the objects
     *
     * @psalm-return T[]
     *
     * @throws UnexpectedValueException
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array;

    /**
     * @param object $object the object instance to save
     */
    public function save(object $object): void;

    /**
     * @param object $object the object instance to remove
     */
    public function remove(object $object): void;
}
