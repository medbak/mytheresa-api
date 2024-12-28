<?php

declare(strict_types=1);

namespace App\Tests\Feature;

use App\Domain\Entity\Category;
use App\Domain\Entity\Discount;
use App\Domain\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductApiTest extends WebTestCase
{
    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();

        $this->entityManager = self::getContainer()
            ->get('doctrine')
            ->getManager();

        $this->entityManager->beginTransaction();
        $this->loadFixtures();
    }

    protected function tearDown(): void
    {
        if ($this->entityManager && $this->entityManager->getConnection()->isTransactionActive()) {
            $this->entityManager->rollback();
            $this->entityManager->close();
        }

        parent::tearDown();
        $this->entityManager = null;
    }

    private function loadFixtures(): void
    {
        $bootsCategory = new Category('boots');
        $sandalsCategory = new Category('sandals');

        $this->entityManager->persist($bootsCategory);
        $this->entityManager->persist($sandalsCategory);

        $products = [
            new Product('000001', 'Expensive Boots', $bootsCategory, 150000),
            new Product('000002', 'Medium Boots', $bootsCategory, 89000),
            new Product('000003', 'Special Discount Boots', $bootsCategory, 71000),
            new Product('000004', 'Basic Sandals', $sandalsCategory, 45000),
            new Product('000005', 'Luxury Sandals', $sandalsCategory, 95000),
        ];

        foreach ($products as $product) {
            $this->entityManager->persist($product);
        }

        $validFrom = new \DateTimeImmutable('2024-01-01');
        $validUntil = new \DateTimeImmutable('2025-12-31');

        $categoryDiscount = Discount::createForCategory(
            $bootsCategory,
            30.0,
            $validFrom,
            $validUntil
        );
        $this->entityManager->persist($categoryDiscount);

        $skuDiscount = Discount::createForSku(
            '000003',
            15.0,
            $validFrom,
            $validUntil
        );
        $this->entityManager->persist($skuDiscount);

        $this->entityManager->flush();
    }

    public function testGetProducts(): void
    {
        $this->client->request('GET', '/products');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $content = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('products', $content);
        $this->assertCount(5, $content['products']);

        $expensiveBoots = $this->findProductBySku($content['products'], '000001');
        $this->assertEquals('boots', $expensiveBoots['category']);
        $this->assertEquals('30%', $expensiveBoots['price']['discount_percentage']);
        $this->assertEquals(105000, $expensiveBoots['price']['final']);

        $specialBoots = $this->findProductBySku($content['products'], '000003');
        $this->assertEquals('30%', $specialBoots['price']['discount_percentage']);

        $basicSandals = $this->findProductBySku($content['products'], '000004');
        $this->assertNull($basicSandals['price']['discount_percentage']);
        $this->assertEquals(45000, $basicSandals['price']['final']);
    }

    public function testGetProductsWithCategoryFilter(): void
    {
        $this->client->request('GET', '/products?category=boots');

        $this->assertResponseIsSuccessful();
        $content = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertCount(3, $content['products']);
        foreach ($content['products'] as $product) {
            $this->assertEquals('boots', $product['category']);
        }
    }

    public function testGetProductsWithPriceFilter(): void
    {
        $this->client->request('GET', '/products?priceLessThan=90000');

        $this->assertResponseIsSuccessful();
        $content = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertCount(3, $content['products']);
        foreach ($content['products'] as $product) {
            $this->assertLessThanOrEqual(90000, $product['price']['original']);
        }
    }

    public function testGetProductsWithCategoryAndPriceFilter(): void
    {
        $this->client->request('GET', '/products?category=boots&priceLessThan=100000');

        $this->assertResponseIsSuccessful();
        $content = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertCount(2, $content['products']);
        foreach ($content['products'] as $product) {
            $this->assertEquals('boots', $product['category']);
            $this->assertLessThanOrEqual(100000, $product['price']['original']);
        }
    }

    private function findProductBySku(array $products, string $sku): ?array
    {
        foreach ($products as $product) {
            if ($product['sku'] === $sku) {
                return $product;
            }
        }

        return null;
    }
}
