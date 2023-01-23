<?php

namespace App\Service\ApiClient;

use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @template T of object
 *
 * @template-implements ApiClientInterface<T>
 */
class ApiClient implements ApiClientInterface
{
    protected HttpClientInterface $client;

    private string $relativeUrl;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client->withOptions([
            'base_uri' => 'https://demo.api-platform.com',
        ]);
        $this->relativeUrl = '/books/';
    }

    public function find(mixed $id): ?object
    {

        $response = $this->client->request('GET', $this->getRelativeUrl(), [
            'headers' => [
                'accept' => 'application/json',
            ],
        ]);
        dump($response);
        $content = $response->getContent();
        dump($content);

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

    protected function getRelativeUrl(): string
    {
        return $this->relativeUrl;
    }
}
