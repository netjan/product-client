<?php

namespace App\Repository;

use App\Entity\Product;
use App\Exception\ConnectionException;
use App\Exception\NotFoundException;
use App\Filter\ProductFilter;
use Symfony\Component\HttpClient\DecoratorTrait;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ProductRepository implements HttpClientInterface
{
    use DecoratorTrait;

    private const RELATIVE_URL = '/booksss';

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
        } catch (ClientException $e) {
            throw new NotFoundException($e);
        } catch (ExceptionInterface $e) {
            throw new ConnectionException($e);
        }

                return $items;
    }
}
