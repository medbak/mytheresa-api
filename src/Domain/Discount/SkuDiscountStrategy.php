<?php

declare(strict_types=1);

namespace App\Domain\Discount;

use App\Domain\Entity\Product;

class SkuDiscountStrategy implements DiscountStrategyInterface
{
    private const array SKU_DISCOUNTS = [
        '000003' => 15.0,
    ];

    public function getDiscount(Product $product): ?float
    {
        return self::SKU_DISCOUNTS[$product->getSku()] ?? null;
    }
}
