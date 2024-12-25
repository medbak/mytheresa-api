<?php

declare(strict_types=1);

namespace App\Domain\Service;

class DiscountCalculator
{
    public function calculateFinalPrice(int $originalPrice, float $discount): int
    {
        return (int) ($originalPrice - ($originalPrice * $discount / 100));
    }
}
