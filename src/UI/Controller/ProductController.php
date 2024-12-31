<?php

declare(strict_types=1);

namespace App\UI\Controller;

use App\Application\Service\ProductService;
use App\UI\Formatter\ValidationErrorFormatter;
use App\UI\Validator\RequestValidator;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @OA\Tag(name="Products")
 */
class ProductController extends AbstractController
{
    public function __construct(
        private readonly ProductService $productService,
        private readonly RequestValidator $requestValidator,
        private readonly ValidationErrorFormatter $errorFormatter,
        private readonly LoggerInterface $logger,
    ) {
    }

    #[Route('/products', name: 'get_products', methods: ['GET'])]
    public function getProducts(Request $request): JsonResponse
    {
        try {
            $constraints = [
                'category' => [
                    'rules' => ['string', 'length_max:50'],
                    'optional' => true,
                ],
                'priceLessThan' => [
                    'rules' => ['numeric', 'positive_or_zero'],
                    'optional' => true,
                ],
                'page' => [
                    'rules' => ['numeric', 'min:1'],
                    'optional' => true,
                ],
            ];

            $violations = $this->requestValidator->validate($request->query->all(), $constraints);

            if (!empty($violations)) {
                return $this->json(
                    $this->errorFormatter->formatValidationErrors($violations),
                    Response::HTTP_BAD_REQUEST
                );
            }

            $page = $request->query->getInt('page', 1);
            $price = $request->query->get('priceLessThan');
            $priceValue = null !== $price ? (int) $price : null;

            $result = $this->productService->getProducts(
                $request->query->get('category'),
                $priceValue,
                $page,
            );

            return $this->json([
                'status' => 'success',
                'products' => $result['products'],
                'has_more' => $result['has_more'],
            ], Response::HTTP_OK);
        } catch (\InvalidArgumentException $e) {
            return $this->json(
                [
                    'status' => 'error',
                    'message' => $e->getMessage(),
                ],
                Response::HTTP_BAD_REQUEST
            );
        } catch (\Exception $e) {
            $this->logger->error('error in :', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->json(
                [
                    'status' => 'error',
                    'message' => 'An unexpected error occurred. Please try again later.',
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
