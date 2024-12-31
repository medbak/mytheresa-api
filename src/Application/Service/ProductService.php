<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Repository\ProductFilterInterface;

class ProductService
{
    public function __construct(
        private readonly ProductFilterInterface $productFilter,
        private readonly ProductResponseFormatter $formatter,
    ) {
    }

    public function getProducts(?string $category, ?int $priceLessThan, int $page = 1): array
    {
        $limit = 5;
        $offset = ($page - 1) * $limit;

        $products = $this->productFilter->findByFilters(
            $category,
            $priceLessThan,
            $limit + 1,
            $offset
        );

        $hasMore = \count($products) > $limit;

        $productsToReturn = \array_slice($products, 0, $limit);

        return $this->formatter->format($productsToReturn, $hasMore);
    }
}
