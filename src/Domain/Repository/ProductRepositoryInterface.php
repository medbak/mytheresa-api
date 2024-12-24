<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Product;

interface ProductRepositoryInterface
{
    /**
     * @return array<Product>
     */
    public function findByFilters(?string $category = null, ?int $priceLessThan = null, int $limit = 5): array;
}
