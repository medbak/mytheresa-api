<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Entity\Product;
use App\Domain\Service\DiscountCalculator;
use App\Domain\Service\DiscountService;

readonly class ProductResponseFormatter
{
    public function __construct(
        private DiscountService $discountService,
        private DiscountCalculator $calculator,
    ) {
    }

    public function format(array $products, bool $hasMore = false): array
    {
        return [
            'products' => array_map(
                [$this, 'formatProduct'],
                $products
            ),
            'has_more' => $hasMore,
        ];
    }

    private function formatProduct(Product $product): array
    {
        $originalPrice = $product->getPrice();
        $discount = $this->discountService->getMaxDiscount($product);

        $finalPrice = $discount
            ? $this->calculator->calculateFinalPrice($originalPrice, $discount)
            : $originalPrice;

        return [
            'sku' => $product->getSku(),
            'name' => $product->getName(),
            'category' => $product->getCategory()->getName(),
            'price' => [
                'original' => $originalPrice,
                'final' => $finalPrice,
                'discount_percentage' => $discount ? \sprintf('%.0f%%', $discount) : null,
                'currency' => 'EUR',
            ],
        ];
    }
}
