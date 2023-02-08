<?php

namespace App\Tests\Fixtures;

use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\ResponseInterface;

class MockClientCallback
{
    public function __invoke(string $method, string $url, array $options = []): ResponseInterface
    {
        $baseUri = implode($options['base_uri']);
        $relativeUri = str_replace($baseUri, '', $url);
        if (false !== $strPosition = strpos($relativeUri, '?')) {
            $relativeUri = substr($relativeUri, 0, $strPosition);
        }
        $matches = explode('/', $relativeUri);
        $resource = $matches[0];
        $id = '';
        if (!empty($matches[1])) {
            $id = $matches[1];
        }

        if ('GET' === $method) {
            return $this->getResponse($resource, $id);
        }
        if ('POST' === $method) {
            return $this->postResponse($resource);
        }
        if ('PUT' === $method) {
            return $this->putResponse($resource, $id);
        }
        if ('DELETE' === $method) {
            return $this->deleteResponse($resource, $id);
        }

        return new MockResponse(json_encode([]));
    }

    private function getResponse(string $resource, string $id): ResponseInterface
    {
        if ('products' === $resource) {
            if ('1' === $id) {
                $dataBody = [
                    'id' => 1, 'name' => 'Name 1', 'amount' => 1,
                ];

                return new MockResponse(json_encode($dataBody));
            }
            if ('404' === $id) {
                $dataBody = [
                    'id' => 0
                ];
                return new MockResponse(json_encode([$dataBody]));
            }
        }

        return new MockResponse(json_encode([]), ['http_code' => 400]);
    }

    private function postResponse(string $resource): ResponseInterface
    {
        if ('products' === $resource) {
            return new MockResponse(json_encode([]), ['http_code' => 201]);
        }

        return new MockResponse(json_encode([]), ['http_code' => 400]);
    }

    private function putResponse(string $resource, string $id): ResponseInterface
    {
        if ('products' === $resource) {
            if ($id) {
                $dataBody = [
                    'id' => 1, 'name' => 'Name 1', 'amount' => 1,
                ];

                return new MockResponse(json_encode($dataBody));
            }

            return new MockResponse(json_encode([]), ['http_code' => 404]);
        }

        return new MockResponse(json_encode([]), ['http_code' => 400]);
    }

    private function deleteResponse(string $resource, string $id): ResponseInterface
    {
        if ('products' === $resource) {
            if ($id) {
                return new MockResponse(json_encode([]), ['http_code' => 204]);
            }

            return new MockResponse(json_encode([]), ['http_code' => 404]);
        }

        return new MockResponse(json_encode([]), ['http_code' => 400]);
    }
}
