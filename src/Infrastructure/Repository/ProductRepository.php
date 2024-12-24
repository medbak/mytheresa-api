<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Product;
use App\Domain\Repository\ProductRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProductRepository extends ServiceEntityRepository implements ProductRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findByFilters(?string $category = null, ?int $priceLessThan = null, int $limit = 5): array
    {
        $qb = $this->createQueryBuilder('p')
            ->setMaxResults($limit);

        if (null !== $category) {
            $qb->andWhere('p.category = :category')
                ->setParameter('category', $category);
        }

        if (null !== $priceLessThan) {
            $qb->join('p.price', 'pr')
                ->andWhere('pr.original <= :price')
                ->setParameter('price', $priceLessThan);
        }

        return $qb->getQuery()->getResult();
    }
}
