<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Entity\Category;
use App\Domain\Entity\Discount;
use App\Domain\Repository\DiscountRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;

readonly class DiscountRepository implements DiscountRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findValidCategoryDiscount(Category $category, \DateTimeImmutable $at): ?Discount
    {
        return $this->entityManager->createQueryBuilder()
            ->select('d')
            ->from(Discount::class, 'd')
            ->where('d.category = :category')
            ->andWhere('d.validFrom <= :at')
            ->andWhere('d.validUntil IS NULL OR d.validUntil >= :at')
            ->setParameters([
                'category' => $category,
                'at' => $at,
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findValidSkuDiscount(string $sku, \DateTimeImmutable $at): ?Discount
    {
        return $this->entityManager->createQueryBuilder()
            ->select('d')
            ->from(Discount::class, 'd')
            ->where('d.sku = :sku')
            ->andWhere('d.validFrom <= :at')
            ->andWhere('d.validUntil IS NULL OR d.validUntil >= :at')
            ->setParameters([
                'sku' => $sku,
                'at' => $at,
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }
}
