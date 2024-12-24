<?php

declare(strict_types=1);

namespace App\Controller;

use App\Application\Service\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    public function __construct(
        private readonly ProductService $productService,
    ) {
    }

    #[Route('/products', name: 'get_products', methods: ['GET'])]
    public function getProducts(Request $request): JsonResponse
    {
        $category = $request->query->get('category');
        $priceLessThan = $request->query->get('priceLessThan');

        if (null !== $priceLessThan) {
            $priceLessThan = (int) $priceLessThan;
        }

        $products = $this->productService->getProducts($category, $priceLessThan);

        return new JsonResponse(['products' => $products]);
    }
}
