<?php

namespace App\Tests\Repository;

use App\Entity\Product;
use App\Exception\ConnectionException;
use App\Exception\DataTransferException;
use App\Exception\NotFoundException;
use App\Filter\ProductFilter;
use App\Repository\ProductRepository;
use App\Tests\Fixtures\Helper;
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

    /**
     * @dataProvider dataForFind
     */
    public function testFind(array $dataBody, Product $expected): void
    {
        $response = new MockResponse(json_encode($dataBody));
        $client = new MockHttpClient($response);
        $testRepository = new ProductRepository($client);
        $actual = $testRepository->find(1);
        $this->assertEquals($expected, $actual);
    }

    public function dataForFind(): \Generator
    {
        $data = [
            [
                'id' => 1, 'name' => 'Name 1', 'amount' => 1,
            ],

        ];
        foreach($data as $dataBody) {
            $product = new Product($dataBody['id']);
            $product->setName($dataBody['name']);
            $product->setAmount($dataBody['amount']);
            yield [
                $dataBody,
                $product,
            ];
        }
    }
    /**
     * @dataProvider dataForFindByProductFilter
     */
    public function testFindByProductFilter(array $expected, ?bool $stock): void
    {
        $response = new MockResponse(json_encode($expected));
        $client = new MockHttpClient($response);
        $testRepository = new ProductRepository($client);
        $filter = new ProductFilter();
        $filter->stock = $stock;
        $actual = $testRepository->findByProductFilter($filter);
        $this->assertSame(count($expected), count($actual));
        foreach ($expected as $item) {
            $isFound = false;
            foreach ($actual as $product) {
                if ($item['id'] === $product->getId()) {
                    $this->assertSame($item['name'], $product->getName());
                    $this->assertSame($item['amount'], $product->getAmount());
                    $isFound = true;
                }
            }
            if (!$isFound) {
                $this->assertTrue(false, sprintf('Not found "item" with id: %d', $item['id']));
            }
        }
    }

    public function dataForFindByProductFilter(): array
    {
        return [
            [
                [
                    [
                        'id' => 1, 'name' => 'Name 1', 'amount' => 1,
                    ],
                    [
                        'id' => 2, 'name' => 'Name 2', 'amount' => 2,
                    ],
                    [
                        'id' => 3, 'name' => 'Name 3', 'amount' => 0,
                    ],
                ],
                null,
            ],
            [
                [
                    [
                        'id' => 1, 'name' => 'Name 1', 'amount' => 1,
                    ],
                    [
                        'id' => 2, 'name' => 'Name 2', 'amount' => 2,
                    ],
                ],
                true,
            ],
            [
                [
                    [
                        'id' => 3, 'name' => 'Name 3', 'amount' => 0,
                    ],
                ],
                false,
            ],
        ];
    }

    public function testShouldThrowNotFoundExceptionWhenClientResponseOKOnFindByProductFilter(): void
    {
        $this->expectException(NotFoundException::class);

        $response = new MockResponse('', ['http_code' => 404]);
        $client = new MockHttpClient($response);
        $testRepository = new ProductRepository($client);
        $filter = new ProductFilter();
        $testRepository->findByProductFilter($filter);
    }

    public function testShouldThrowConnectionExceptionWhenClientNotResponseOKOnFindByProductFilter(): void
    {
        $this->expectException(ConnectionException::class);

        $response = new MockResponse('', ['http_code' => 500]);
        $client = new MockHttpClient($response);
        $testRepository = new ProductRepository($client);
        $filter = new ProductFilter();
        $testRepository->findByProductFilter($filter);
    }

    public function testShouldThrowConnectionExceptionWhenClientResponseErrorOnFindByProductFilter(): void
    {
        $this->expectException(ConnectionException::class);

        $response = new MockResponse('', ['error' => 'error']);
        $client = new MockHttpClient($response);
        $testRepository = new ProductRepository($client);
        $filter = new ProductFilter();
        $testRepository->findByProductFilter($filter);
    }

    public function testShouldThrowDataTransferExceptionWhenClientResponseWrongDataOnFindByProductFilter(): void
    {
        $this->expectException(DataTransferException::class);

        $response = new MockResponse('');
        $client = new MockHttpClient($response);
        $testRepository = new ProductRepository($client);
        $filter = new ProductFilter();
        $testRepository->findByProductFilter($filter);
    }

    /**
     * @dataProvider dataForCreateProduct
     */
    public function testCreateProduct(array $item, ?Product $expected): void
    {
        $client = new MockHttpClient();
        $testRepository = new ProductRepository($client);
        $actual = Helper::invokeMethod($testRepository, 'createProduct', [$item]);
        $this->assertEquals($expected, $actual);
    }

    public function dataForCreateProduct(): array
    {
        $product1 = new Product(1);
        $product1->setName('Name 1');
        $product1->setAmount(1);

        return [
            [
                [
                    'id' => 0, 'name' => 'Name 1', 'amount' => 1,
                ],
                null,
            ],
            [
                [
                    'id' => 1, 'name' => 'Name 1', 'amount' => 1,
                ],
                $product1,
            ],
        ];
    }
}
