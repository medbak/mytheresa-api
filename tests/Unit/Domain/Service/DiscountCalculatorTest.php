<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Service;

use App\Domain\Service\DiscountCalculator;
use PHPUnit\Framework\TestCase;

class DiscountCalculatorTest extends TestCase
{
    private DiscountCalculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new DiscountCalculator();
    }

    /**
     * @dataProvider discountCases
     */
    public function testCalculateFinalPrice(int $originalPrice, float $discount, int $expectedPrice): void
    {
        $result = $this->calculator->calculateFinalPrice($originalPrice, $discount);
        $this->assertEquals($expectedPrice, $result);
    }

    public static function discountCases(): array
    {
        return [
            'thirty_percent_off' => [100000, 30, 70000],
            'fifteen_percent_off' => [100000, 15, 85000],
            'no_discount' => [100000, 0, 100000],
        ];
    }
}
