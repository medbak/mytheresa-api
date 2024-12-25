<?php

declare(strict_types=1);

namespace App\Application\DTO\Product;

use App\Domain\Entity\Product;

final readonly class ProductResponse
{
    public function __construct(
        public string $sku,
        public string $name,
        public string $category,
        public array $price,
    ) {
    }

    public static function fromEntity(Product $product, ?float $discount = null): self
    {
        $originalPrice = $product->getPrice();
        $finalPrice = $discount
            ? $originalPrice - ($originalPrice * $discount / 100)
            : $originalPrice;

        return new self(
            $product->getSku(),
            $product->getName(),
            $product->getCategory()->getName(),
            [
                'original' => $originalPrice,
                'final' => (int) $finalPrice,
                'discount_percentage' => $discount ? \sprintf('%.0f%%', $discount) : null,
                'currency' => 'EUR',
            ]
        );
    }
}
