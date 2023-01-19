<?php

namespace App\Repository;

use App\Entity\Product;
use App\Filter\ProductFilter;

class ProductRepository
{
    /**
     * @return Product[]
     */
    public function findAll(): array
    {
        return [];
    }

    /**
     * @return Product[]
     */
    public function findByProductFilter(ProductFilter $filter): array
    {
        return [];
    }
}
