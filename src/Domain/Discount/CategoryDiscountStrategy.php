<?php

declare(strict_types=1);

namespace App\Domain\Discount;

use App\Domain\Entity\Product;

class CategoryDiscountStrategy implements DiscountStrategyInterface
{
    private const float BOOTS_DISCOUNT = 30.0;

    public function getDiscount(Product $product): ?float
    {
        return 'boots' === $product->getCategory() ? self::BOOTS_DISCOUNT : null;
    }
}
