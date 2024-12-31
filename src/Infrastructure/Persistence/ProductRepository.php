<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Entity\Product;
use App\Domain\Repository\ProductFilterInterface;
use App\Domain\Repository\ProductFinderInterface;
use App\Infrastructure\Cache\ProductCache;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Psr\Cache\InvalidArgumentException;

class ProductRepository extends EntityRepository implements ProductFinderInterface, ProductFilterInterface
{
    private ProductCache $cache;

    public function __construct(EntityManagerInterface $entityManager, ProductCache $cache)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata(Product::class));
        $this->cache = $cache;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function findBySku(string $sku): ?Product
    {
        return $this->cache->get($sku) ??
            $this->findOneBy(['sku' => $sku]);
    }

    public function findByFilters(?string $category, ?int $priceLessThan, int $limit, int $offset = 0): array
    {
        $qb = $this->createQueryBuilder('p')
            ->orderBy('p.sku', 'ASC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        if ($category) {
            $qb->join('p.category', 'c')
                ->andWhere('c.name = :category')
                ->setParameter('category', $category);
        }

        if ($priceLessThan) {
            $qb->andWhere('p.price <= :price')
                ->setParameter('price', $priceLessThan);
        }

        return $qb->getQuery()->getResult();
    }
}
