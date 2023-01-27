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

    private const RELATIVE_URL = '/books';

    /**
     * @return Product[]
     */
    public function findByProductFilter(ProductFilter $filter): array
    {
        $query = [];
        if (null !== $filter->stock) {
            $query['archived'] = $filter->stock;
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

    private function createProduct(array $item): ?Product
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        $id = (int) $propertyAccessor->getValue($item, '[isbn]');
        if (1 > $id) {
            return null;
        }

        $product = new Product($id);
        $product->setName((string) $propertyAccessor->getValue($item, '[title]'));
        $a = (array) $propertyAccessor->getValue($item, '[reviews]');
        $product->setAmount(count($a));

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
