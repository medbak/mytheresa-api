<?php

declare(strict_types=1);

namespace App\Infrastructure\Cache;

use App\Domain\Entity\Product;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class ProductCache
{
    private const int TTL = 3600; // 1 hour

    public function __construct(
        private readonly CacheInterface $cache,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function get(string $sku): ?Product
    {
        return $this->cache->get(
            $this->getCacheKey($sku),
            function (ItemInterface $item): ?Product {
                $item->expiresAfter(self::TTL);

                return null;
            }
        );
    }

    /**
     * @throws InvalidArgumentException
     */
    public function set(Product $product): void
    {
        $this->cache->delete($this->getCacheKey($product->getSku()));
        $this->cache->get(
            $this->getCacheKey($product->getSku()),
            function (ItemInterface $item) use ($product): Product {
                $item->expiresAfter(self::TTL);

                return $product;
            }
        );
    }

    private function getCacheKey(string $sku): string
    {
        return \sprintf('product_%s', $sku);
    }
}
