<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class Price
{
    #[ORM\Column(type: 'integer')]
    private int $original;

    #[ORM\Column(type: 'integer')]
    private int $final;

    #[ORM\Column(type: 'string', length: 4)]
    private string $currency = 'EUR';

    #[ORM\Column(type: 'string', length: 4, nullable: true)]
    private ?string $discountPercentage = null;

    public function __construct(int $amount)
    {
        $this->original = $amount;
        $this->final = $amount;
    }

    public function applyDiscount(float $percentage): void
    {
        $discountAmount = (int) ($this->original * ($percentage / 100));
        $this->final = $this->original - $discountAmount;
        $this->discountPercentage = $percentage.'%';
    }

    public function toArray(): array
    {
        return [
            'original' => $this->original,
            'final' => $this->final,
            'discount_percentage' => $this->discountPercentage,
            'currency' => $this->currency,
        ];
    }
}
