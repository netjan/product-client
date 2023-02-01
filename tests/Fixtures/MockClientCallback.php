<?php

namespace App\Tests\Fixtures;

use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\ResponseInterface;

class MockClientCallback
{
    public function __invoke(string $method, string $url, array $options = []): ResponseInterface
    {
        return new MockResponse(json_encode([]));
    }
}
