<?php

declare(strict_types=1);

namespace App\Domain\Service\DiscountStrategy;

use App\Domain\Entity\Product;

interface DiscountStrategyInterface
{
    public function getDiscount(Product $product, \DateTimeImmutable $at): ?float;
}
