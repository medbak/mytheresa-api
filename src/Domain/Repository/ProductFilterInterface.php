<?php

declare(strict_types=1);

namespace App\Domain\Repository;

interface ProductFilterInterface
{
    public function findByFilters(?string $category, ?int $priceLessThan, int $limit = 5): array;
}
