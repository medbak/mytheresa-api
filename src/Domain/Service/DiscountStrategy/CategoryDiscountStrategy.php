<?php

declare(strict_types=1);

namespace App\Domain\Service\DiscountStrategy;

use App\Domain\Entity\Product;
use App\Domain\Repository\DiscountRepositoryInterface;

readonly class CategoryDiscountStrategy implements DiscountStrategyInterface
{
    public function __construct(
        private DiscountRepositoryInterface $discountRepository,
    ) {
    }

    public function getDiscount(Product $product, \DateTimeImmutable $at): ?float
    {
        $discount = $this->discountRepository->findValidCategoryDiscount(
            $product->getCategory(),
            $at
        );

        return $discount?->getPercentage();
    }
}
