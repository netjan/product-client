<?php

namespace App\Repository;

use App\Entity\Product;
use App\Exception\ConnectionException;
use App\Exception\DataTransferException;
use App\Exception\NotFoundException;
use App\Filter\ProductFilter;
use Symfony\Component\HttpClient\DecoratorTrait;
use Symfony\Component\HttpClient\Exception\JsonException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ProductRepository implements HttpClientInterface
{
    use DecoratorTrait;

    private const RELATIVE_URL = 'products';

    public function find(string $id): ?Product
    {
        $response = $this->request('GET', self::RELATIVE_URL.'/'.$id);

        $statusCode = $this->getStatusCode($response);
        if (Response::HTTP_NOT_FOUND === $statusCode) {
            return null;
        }
        if (Response::HTTP_OK !== $statusCode) {
            throw new ConnectionException();
        }
        $item = $this->responseToArray($response);

        return $this->createProduct($item);
    }

    /**
     * @return Product[]
     */
    public function findByProductFilter(ProductFilter $filter): array
    {
        $query = [];
        if (null !== $filter->stock) {
            $query['stock'] = $filter->stock;
        }

        $response = $this->request('GET', self::RELATIVE_URL, [
            'query' => $query,
        ]);

        $statusCode = $this->getStatusCode($response);
        if (Response::HTTP_NOT_FOUND === $statusCode) {
            throw new NotFoundException();
        }
        if (Response::HTTP_OK !== $statusCode) {
            throw new ConnectionException();
        }

        $items = $this->responseToArray($response);
        $result = [];
        /** @var array $item */
        foreach ($items as $item) {
            if (null !== $product = $this->createProduct($item)) {
                $result[] = $product;
            }
        }

        return $result;
    }

    public function save(Product $product): void
    {
        $id = (string) $product->getId();
        $response = $this->request('PUT', self::RELATIVE_URL.'/'.$id, [
            'json' => [
                'name' => $product->getName(),
                'amount' => $product->getAmount(),
            ],
        ]);
        $statusCode = $this->getStatusCode($response);
        if (Response::HTTP_NOT_FOUND === $statusCode) {
            throw new NotFoundException();
        }
        if (Response::HTTP_OK !== $statusCode) {
            throw new ConnectionException();
        }
        $item = $this->responseToArray($response);

        $propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->disableExceptionOnInvalidPropertyPath()
            ->getPropertyAccessor();

        $product->setName((string) $propertyAccessor->getValue($item, '[name]'));
        $product->setAmount((int) $propertyAccessor->getValue($item, '[amount]'));
    }

    public function remove(string $id): void
    {
    }

    private function createProduct(array $item): ?Product
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->disableExceptionOnInvalidPropertyPath()
            ->getPropertyAccessor();

        $id = (int) $propertyAccessor->getValue($item, '[id]');
        if (1 > $id) {
            return null;
        }

        $product = new Product($id);
        $product->setName((string) $propertyAccessor->getValue($item, '[name]'));
        $product->setAmount((int) $propertyAccessor->getValue($item, '[amount]'));

        return $product;
    }

    private function responseToArray(ResponseInterface $response): array
    {
        try {
            $items = $response->toArray();
        } catch (JsonException $e) {
            throw new DataTransferException($e);
        }

        return $items;
    }

    private function getStatusCode(ResponseInterface $response): int
    {
        try {
            $statusCode = $response->getStatusCode();
        } catch (ExceptionInterface $e) {
            throw new ConnectionException($e);
        }

        return $statusCode;
    }
}
