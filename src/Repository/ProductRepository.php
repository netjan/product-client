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

    /**
     * @throws ConnectionException
     * @throws NotFoundException
     * @throws DataTransferException
     */
    public function find(string $id): ?Product
    {
        $response = $this->request('GET', self::RELATIVE_URL.'/'.$id);

        $this->checkStatusCode($response);
        $item = $this->responseToArray($response);

        return $this->createProduct($item);
    }

    /**
     * @return Product[]
     *
     * @throws ConnectionException
     * @throws NotFoundException
     * @throws DataTransferException
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

        $this->checkStatusCode($response);
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

    /**
     * @throws ConnectionException
     * @throws NotFoundException
     */
    public function save(Product $product): void
    {
        $dataBody = [
            'name' => $product->getName(),
            'amount' => $product->getAmount(),
        ];
        if (null === $id = $product->getId()) {
            $response = $this->request('POST', self::RELATIVE_URL, [
                'json' => $dataBody,
            ]);
            $expectedSatusCode = Response::HTTP_CREATED;
        } else {
            $response = $this->request('PUT', self::RELATIVE_URL.'/'.$id, [
                'json' => $dataBody,
            ]);
            $expectedSatusCode = Response::HTTP_OK;
        }

        $this->checkStatusCode($response, $expectedSatusCode);
    }

    public function remove(string $id): void
    {
        $response = $this->request('DELETE', self::RELATIVE_URL.'/'.$id);
        $this->checkStatusCode($response, Response::HTTP_NO_CONTENT);
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

    /**
     * @throws DataTransferException
     */
    private function responseToArray(ResponseInterface $response): array
    {
        try {
            $items = $response->toArray();
        } catch (JsonException $e) {
            throw new DataTransferException($e);
        }

        return $items;
    }

    /**
     * @throws ConnectionException
     * @throws NotFoundException
     */
    private function checkStatusCode(
        ResponseInterface $response,
        int $expectedSatusCode = Response::HTTP_OK
    ): void {
        try {
            $statusCode = $response->getStatusCode();
        } catch (ExceptionInterface $e) {
            throw new ConnectionException($e);
        }

        if (Response::HTTP_NOT_FOUND === $statusCode) {
            throw new NotFoundException();
        }
        if ($expectedSatusCode !== $statusCode) {
            throw new ConnectionException();
        }
    }
}
