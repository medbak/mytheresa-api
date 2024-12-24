<?php

declare(strict_types=1);

namespace App\Domain\Discount;

use App\Domain\Entity\Product;

interface DiscountStrategyInterface
{
    public function getDiscount(Product $product): ?float;
}
