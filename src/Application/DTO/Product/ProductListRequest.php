<?php

declare(strict_types=1);

namespace App\Application\DTO\Product;

final readonly class ProductListRequest
{
    public function __construct(
        private ?string $category = null,
        private ?int $priceLessThan = null,
        private int $limit = 5,
    ) {
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function getPriceLessThan(): ?int
    {
        return $this->priceLessThan;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }
}
