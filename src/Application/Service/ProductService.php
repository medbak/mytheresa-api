<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Discount\DiscountStrategyInterface;
use App\Domain\Repository\ProductRepositoryInterface;

readonly class ProductService
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private DiscountStrategyInterface $discountStrategy,
    ) {
    }

    public function getProducts(?string $category = null, ?int $priceLessThan = null): array
    {
        $products = $this->productRepository->findByFilters($category, $priceLessThan);

        foreach ($products as $product) {
            $discount = $this->discountStrategy->getDiscount($product);
            if (null !== $discount) {
                $product->applyDiscount($discount);
            }
        }

        return array_map(fn ($product) => [
            'sku' => $product->getSku(),
            'name' => $product->getName(),
            'category' => $product->getCategory(),
            'price' => $product->getPrice()->toArray(),
        ], $products);
    }
}
