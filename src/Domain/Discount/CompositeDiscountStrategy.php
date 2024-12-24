<?php

declare(strict_types=1);

namespace App\Domain\Discount;

use App\Domain\Entity\Product;

class CompositeDiscountStrategy implements DiscountStrategyInterface
{
    /** @var array<DiscountStrategyInterface> */
    private array $strategies;

    public function __construct(array $strategies)
    {
        $this->strategies = $strategies;
    }

    public function getDiscount(Product $product): ?float
    {
        $maxDiscount = null;

        foreach ($this->strategies as $strategy) {
            $discount = $strategy->getDiscount($product);
            if (null !== $discount && (null === $maxDiscount || $discount > $maxDiscount)) {
                $maxDiscount = $discount;
            }
        }

        return $maxDiscount;
    }
}
