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

    public function getProducts(?string $category, ?int $priceLessThan): array
    {
        $products = $this->productFilter->findByFilters($category, $priceLessThan);

        return $this->formatter->format(
            \array_slice($products, 0, 5)
        );
    }
}
