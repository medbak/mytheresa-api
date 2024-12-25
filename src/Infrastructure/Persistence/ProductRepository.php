<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Entity\Product;
use App\Domain\Repository\ProductFilterInterface;
use App\Domain\Repository\ProductFinderInterface;
use App\Infrastructure\Cache\ProductCache;
use Doctrine\ORM\EntityManagerInterface;

readonly class ProductRepository implements ProductFinderInterface, ProductFilterInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProductCache $cache,
    ) {
    }

    public function findBySku(string $sku): ?Product
    {
        return $this->cache->get($sku) ??
            $this->entityManager->getRepository(Product::class)->findOneBy(['sku' => $sku]);
    }

    public function findByFilters(?string $category, ?int $priceLessThan, int $limit = 5): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(Product::class, 'p')
            ->orderBy('p.sku', 'ASC')
            ->setMaxResults($limit);

        if ($category) {
            $qb->andWhere('p.category.name = :category')
                ->setParameter('category', $category);
        }

        if ($priceLessThan) {
            $qb->andWhere('p.price <= :price')
                ->setParameter('price', $priceLessThan);
        }

        return $qb->getQuery()->getResult();
    }
}
