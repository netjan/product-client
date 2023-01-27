<?php

namespace App\Tests\Repository;

use App\Repository\ProductRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class ProductRepositoryTest extends TestCase
{
    public function testCreate(): void
    {
        $client = new MockHttpClient();
        $testRepository = new ProductRepository($client);
        $this->assertInstanceOf(ProductRepository::class, $testRepository);
    }

    public function testResponseToArray(): void
    {
        $items = [
            [
                'id' => 1, 'name' => 'Nazwa', 'amount' => 1,
            ],
            [
                'id' => 2, 'name' => 'Nazwa', 'amount' => 1,
            ],
        ];


        $responses = [
            new MockResponse(json_encode($items)),
        ];
        $client = new MockHttpClient($responses);
        $testRepository = new ProductRepository($client);
        $response = $client->request('GET', '/books');
        $state = $this->invokeMethod($testRepository, 'responseToArray', array($response));
        $this->assertEquals($items, $state);
    }

    public function invokeMethod(&$object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
