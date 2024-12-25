<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Domain\Entity\Category;
use App\Domain\Entity\Discount;
use App\Domain\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Categories
        $categories = [
            'boots' => new Category('boots'),
            'sandals' => new Category('sandals'),
            'sneakers' => new Category('sneakers'),
        ];

        foreach ($categories as $category) {
            $manager->persist($category);
        }

        // Products
        $products = [
            [
                'sku' => '000001',
                'name' => 'BV Lean leather ankle boots',
                'category' => 'boots',
                'price' => 89000,
            ],
            [
                'sku' => '000002',
                'name' => 'BV Lean leather ankle boots',
                'category' => 'boots',
                'price' => 99000,
            ],
            [
                'sku' => '000003',
                'name' => 'Ashlington leather ankle boots',
                'category' => 'boots',
                'price' => 71000,
            ],
            [
                'sku' => '000004',
                'name' => 'Naima embellished suede sandals',
                'category' => 'sandals',
                'price' => 79500,
            ],
            [
                'sku' => '000005',
                'name' => 'Nathane leather sneakers',
                'category' => 'sneakers',
                'price' => 59000,
            ],
        ];

        foreach ($products as $data) {
            $product = new Product(
                $data['sku'],
                $data['name'],
                $categories[$data['category']],
                $data['price']
            );
            $manager->persist($product);
        }

        // Discounts
        $validFrom = new \DateTimeImmutable('2024-01-01');
        $validUntil = new \DateTimeImmutable('2024-12-31');

        $bootsDiscount = Discount::createForCategory(
            $categories['boots'],
            30.0,
            $validFrom,
            $validUntil
        );
        $manager->persist($bootsDiscount);

        $skuDiscount = Discount::createForSku(
            '000003',
            15.0,
            $validFrom,
            $validUntil
        );
        $manager->persist($skuDiscount);

        $manager->flush();
    }
}
