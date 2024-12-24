<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\ValueObject\Price;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'products')]
class Product
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 6)]
    private string $sku;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 50)]
    private string $category;

    #[ORM\Embedded(class: Price::class)]
    private Price $price;

    public function __construct(
        string $sku,
        string $name,
        string $category,
        int $price,
    ) {
        $this->sku = $sku;
        $this->name = $name;
        $this->category = $category;
        $this->price = new Price($price);
    }

    public function getSku(): string
    {
        return $this->sku;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getPrice(): Price
    {
        return $this->price;
    }

    public function applyDiscount(float $percentage): void
    {
        $this->price->applyDiscount($percentage);
    }
}
