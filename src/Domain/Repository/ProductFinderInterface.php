<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Product;

interface ProductFinderInterface
{
    public function findBySku(string $sku): ?Product;
}
