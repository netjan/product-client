<?php

namespace App\Repository;

use App\Entity\Product;
use App\Filter\ProductFilter;
use App\Service\ApiClient\ApiClientInterface;

class ProductRepository
{
    private ApiClientInterface $client;

    public function __construct(ApiClientInterface $client)
    {
        $this->client = $client;
    }


    /**
     * @return Product[]
     */
    public function findAll(): array
    {
        $this->client->find(1);
        return [];
    }

    /**
     * @return Product[]
     */
    public function findByProductFilter(ProductFilter $filter): array
    {
        $this->client->find(1);
        return [];
    }
}
