<?php

declare(strict_types=1);

namespace migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20241224000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create optimized schema for large product catalog';
    }

    public function up(Schema $schema): void
    {
        // Categories table
        $this->addSql('CREATE TABLE categories (
            id INT AUTO_INCREMENT NOT NULL,
            name VARCHAR(50) NOT NULL,
            UNIQUE INDEX UNIQ_NAME (name),
            PRIMARY KEY(id)
        ) ENGINE = InnoDB');

        // Main products table
        $this->addSql('CREATE TABLE products (
            id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL,
            category_id INT NOT NULL,
            sku VARCHAR(6) NOT NULL,
            name VARCHAR(255) NOT NULL,
            price INT UNSIGNED NOT NULL,
            created_at DATETIME NOT NULL,
            UNIQUE INDEX UNIQ_SKU (sku),
            INDEX idx_category_price (category_id, price),
            INDEX idx_created_at (created_at),
            PRIMARY KEY(id),
            CONSTRAINT FK_CATEGORY FOREIGN KEY (category_id)
                REFERENCES categories (id)
        ) ENGINE = InnoDB ');

        // Discounts table
        $this->addSql('CREATE TABLE discounts (
            id INT AUTO_INCREMENT NOT NULL,
            category_id INT DEFAULT NULL,
            sku VARCHAR(6) DEFAULT NULL,
            percentage DECIMAL(5,2) UNSIGNED NOT NULL,
            valid_from DATETIME NOT NULL,
            valid_until DATETIME DEFAULT NULL,
            created_at DATETIME NOT NULL,
            UNIQUE INDEX UNIQ_CATEGORY (category_id),
            UNIQUE INDEX UNIQ_SKU (sku),
            INDEX idx_validity (valid_from, valid_until),
            PRIMARY KEY(id),
            CONSTRAINT FK_DISCOUNT_CATEGORY FOREIGN KEY (category_id)
                REFERENCES categories (id),
            CONSTRAINT check_single_discount_type 
                CHECK ((sku IS NULL AND category_id IS NOT NULL) 
                    OR (sku IS NOT NULL AND category_id IS NULL))
        ) ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TRIGGER IF EXISTS after_product_insert');
        $this->addSql('DROP TRIGGER IF EXISTS after_product_update');
        $this->addSql('DROP TABLE product_prices');
        $this->addSql('DROP TABLE products_archive');
        $this->addSql('DROP TABLE products');
        $this->addSql('DROP TABLE discounts');
        $this->addSql('DROP TABLE categories');
    }
}
