<?php

declare(strict_types=1);

namespace App\UI\Controller;

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
        return new JsonResponse(
            $this->productService->getProducts(
                $request->query->get('category'),
                $request->query->get('priceLessThan')
                    ? (int) $request->query->get('priceLessThan')
                    : null
            )
        );
    }
}
