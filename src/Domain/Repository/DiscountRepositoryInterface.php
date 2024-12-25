<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Category;
use App\Domain\Entity\Discount;

interface DiscountRepositoryInterface
{
    public function findValidCategoryDiscount(Category $category, \DateTimeImmutable $at): ?Discount;

    public function findValidSkuDiscount(string $sku, \DateTimeImmutable $at): ?Discount;
}
