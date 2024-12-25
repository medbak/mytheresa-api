<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'discounts')]
class Discount
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 6, unique: true, nullable: true)]
    private ?string $sku = null;

    #[ORM\ManyToOne(targetEntity: Category::class)]
    #[ORM\JoinColumn(unique: true, nullable: true)]
    private ?Category $category = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    private float $percentage;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $validFrom;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $validUntil;

    private function __construct(
        float $percentage,
        \DateTimeImmutable $validFrom,
        ?\DateTimeImmutable $validUntil = null,
    ) {
        $this->validatePercentage($percentage);
        $this->validateDates($validFrom, $validUntil);

        $this->percentage = $percentage;
        $this->validFrom = $validFrom;
        $this->validUntil = $validUntil;
    }

    public static function createForSku(
        string $sku,
        float $percentage,
        \DateTimeImmutable $validFrom,
        ?\DateTimeImmutable $validUntil = null,
    ): self {
        self::validateSku($sku);

        $discount = new self($percentage, $validFrom, $validUntil);
        $discount->sku = $sku;

        return $discount;
    }

    public static function createForCategory(
        Category $category,
        float $percentage,
        \DateTimeImmutable $validFrom,
        ?\DateTimeImmutable $validUntil = null,
    ): self {
        $discount = new self($percentage, $validFrom, $validUntil);
        $discount->category = $category;

        return $discount;
    }

    private static function validateSku(string $sku): void
    {
        if (6 !== \strlen($sku)) {
            throw new \InvalidArgumentException('SKU must be exactly 6 characters');
        }
    }

    private function validatePercentage(float $percentage): void
    {
        if ($percentage <= 0 || $percentage >= 100) {
            throw new \InvalidArgumentException('Percentage must be between 0 and 100');
        }
    }

    private function validateDates(\DateTimeImmutable $validFrom, ?\DateTimeImmutable $validUntil): void
    {
        if (null !== $validUntil && $validFrom > $validUntil) {
            throw new \InvalidArgumentException('Valid from date must be before valid until date');
        }
    }

    public function isValidAt(\DateTimeImmutable $date): bool
    {
        if ($date < $this->validFrom) {
            return false;
        }

        if (null !== $this->validUntil && $date > $this->validUntil) {
            return false;
        }

        return true;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPercentage(): float
    {
        return $this->percentage;
    }

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }
}
