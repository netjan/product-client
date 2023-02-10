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
use Symfony\Component\HttpFoundation\Response;

class ProductRepositoryTest extends TestCase
{
    public function testCreate(): void
    {
        $client = new MockHttpClient();
        $testRepository = new ProductRepository($client);
        $this->assertInstanceOf(ProductRepository::class, $testRepository);
    }

    /**
     * @dataProvider dataProviderFind
     */
    public function testFind(array $dataBody, Product $expected): void
    {
        $response = new MockResponse(json_encode($dataBody));
        $client = new MockHttpClient($response);
        $testRepository = new ProductRepository($client);
        $actual = $testRepository->find(1);
        $this->assertEquals($expected, $actual);
    }

    public function dataProviderFind(): \Generator
    {
        $data = [
            [
                'id' => 1, 'name' => 'Name 1', 'quantity' => 1,
            ],
        ];
        foreach ($data as $dataBody) {
            $product = $this->createProduct($dataBody);
            yield [
                $dataBody,
                $product,
            ];
        }
    }

    /**
     * @dataProvider dataProviderFindByProductFilter
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
                    $this->assertSame($item['quantity'], $product->getQuantity());
                    $isFound = true;
                }
            }
            if (!$isFound) {
                $this->assertTrue(false, sprintf('Not found "item" with id: %d', $item['id']));
            }
        }
    }

    public function dataProviderFindByProductFilter(): array
    {
        return [
            [
                [
                    [
                        'id' => 1, 'name' => 'Name 1', 'quantity' => 1,
                    ],
                    [
                        'id' => 2, 'name' => 'Name 2', 'quantity' => 2,
                    ],
                    [
                        'id' => 3, 'name' => 'Name 3', 'quantity' => 0,
                    ],
                ],
                null,
            ],
            [
                [
                    [
                        'id' => 1, 'name' => 'Name 1', 'quantity' => 1,
                    ],
                    [
                        'id' => 2, 'name' => 'Name 2', 'quantity' => 2,
                    ],
                ],
                true,
            ],
            [
                [
                    [
                        'id' => 3, 'name' => 'Name 3', 'quantity' => 0,
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
     * @dataProvider dataProviderCreateProduct
     */
    public function testCreateProduct(array $item, ?Product $expected): void
    {
        $client = new MockHttpClient();
        $testRepository = new ProductRepository($client);
        $actual = Helper::invokeMethod($testRepository, 'createProduct', [$item]);
        $this->assertEquals($expected, $actual);
    }

    public function dataProviderCreateProduct(): array
    {
        $data = [
            'id' => 1, 'name' => 'Name 1', 'quantity' => 1,
        ];
        $product1 = $this->createProduct($data);

        return [
            [
                [
                    'id' => 0, 'name' => 'Name 1', 'quantity' => 1,
                ],
                null,
            ],
            [
                [
                    'id' => 1, 'name' => 'Name 1', 'quantity' => 1,
                ],
                $product1,
            ],
        ];
    }

    /**
     * @dataProvider dataProviderSave
     */
    public function testSave(Product $product, int $responseStatusCode): void
    {
        $response = new MockResponse('', ['http_code' => $responseStatusCode]);
        $client = new MockHttpClient($response);
        $testRepository = new ProductRepository($client);
        $testRepository->save($product);

        // NotExpectException
        $this->assertTrue(true);
    }

    public function dataProviderSave(): \Generator
    {
        $data = [
            [
                [
                    'id' => 1, 'name' => 'Name 1', 'quantity' => 1,
                ],
                Response::HTTP_OK,
            ],
            [
                [
                    'id' => null, 'name' => 'Name 1', 'quantity' => 1,
                ],
                Response::HTTP_CREATED,
            ],
        ];
        foreach ($data as $item) {
            $product = $this->createProduct($item[0]);
            yield [
                $product,
                $item[1],
            ];
        }
    }

    public function testRemove(): void
    {
        $response = new MockResponse('', ['http_code' => Response::HTTP_NO_CONTENT]);
        $client = new MockHttpClient($response);
        $testRepository = new ProductRepository($client);
        $testRepository->remove(1);

        // NotExpectException
        $this->assertTrue(true);
    }

    private function createProduct(array $data): Product
    {
        $product = new Product($data['id']);
        $product->setName($data['name']);
        $product->setQuantity($data['quantity']);

        return $product;
    }
}
