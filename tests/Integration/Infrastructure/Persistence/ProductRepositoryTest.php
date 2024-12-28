<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure\Persistence;

use App\Domain\Entity\Category;
use App\Domain\Entity\Product;
use App\Infrastructure\Cache\ProductCache;
use App\Infrastructure\Persistence\ProductRepository;
use App\Tests\BaseTestCase;

class ProductRepositoryTest extends BaseTestCase
{
    private ProductRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $cache = static::getContainer()->get(ProductCache::class);

        $this->repository = new ProductRepository(
            $this->entityManager,
            $cache
        );
    }

    public function testFindByFilters(): void
    {
        // Create test data
        $category = new Category('boots');
        $this->entityManager->persist($category);

        $product = new Product('000001', 'Test Boot', $category, 89000);
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        // Test filter by category
        $results = $this->repository->findByFilters('boots', null, 5);
        $this->assertCount(1, $results);
        $this->assertEquals('000001', $results[0]->getSku());

        // Test price filter
        $results = $this->repository->findByFilters(null, 90000, 5);
        $this->assertCount(1, $results);

        // Test both filters
        $results = $this->repository->findByFilters('boots', 90000, 5);
        $this->assertCount(1, $results);
    }
}
