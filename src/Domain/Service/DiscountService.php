<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Entity\Product;
use App\Domain\Service\DiscountStrategy\DiscountStrategyInterface;

readonly class DiscountService
{
    private array $strategies;

    public function __construct(array $strategies = [])
    {
        $this->strategies = $strategies;
    }

    public function getMaxDiscount(Product $product): ?float
    {
        $now = new \DateTimeImmutable();
        $discounts = array_map(
            fn (DiscountStrategyInterface $strategy) => $strategy->getDiscount($product, $now),
            $this->strategies
        );

        $validDiscounts = array_filter($discounts);

        return !empty($validDiscounts) ? max($validDiscounts) : null;
    }
}
