<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'products')]
#[ORM\Index(columns: ['category_id', 'price'])]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 6, unique: true)]
    private string $sku;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\ManyToOne(targetEntity: Category::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Category $category;

    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private int $price;

    public function __construct(
        string $sku,
        string $name,
        Category $category,
        int $price,
    ) {
        $this->sku = $sku;
        $this->name = $name;
        $this->category = $category;
        $this->price = $price;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSku(): string
    {
        return $this->sku;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function getPrice(): int
    {
        return $this->price;
    }
}
