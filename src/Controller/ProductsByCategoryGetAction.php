<?php

namespace App\Controller;

use App\Model\ErrorResponse;
use App\Model\ProductListResponse;
use App\Service\ProductService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[
    OA\Response(
        response: 200,
        description: 'Return published product by category',
        content: new Model(type: ProductListResponse::class)
    ),
    OA\Response(
        response: 404,
        description: 'product category not found',
        content: new Model(type: ErrorResponse::class)
    )
]
#[Route(path: '/api/category/{id}/products', methods: 'GET')]
final class ProductsByCategoryGetAction extends AbstractController
{
    public function __construct(
        private readonly ProductService $productService,
    ) {
    }

    public function __invoke(int $id): Response
    {
        return $this->json($this->productService->getProductsByCategory($id));
    }
}
