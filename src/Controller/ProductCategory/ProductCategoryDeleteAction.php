<?php

namespace App\Controller\ProductCategory;

use App\Model\ErrorResponse;
use App\Model\ProductCategoryListResponse;
use App\Service\ProductCategoryService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[
    OA\Response(
        response: 200,
        description: 'Delete product category',
    ),
    OA\Response(
        response: 400,
        description: 'Product category not empty',
        content: new Model(type: ErrorResponse::class)
    )
]
#[Route(path: '/api/editor/product/category/{id}/delete', methods: 'DELETE')]
final class ProductCategoryDeleteAction extends AbstractController
{
    public function __construct(
        private readonly ProductCategoryService $productCategoryService,
    ) {
    }

    public function __invoke(int $id): JsonResponse
    {
        $this->productCategoryService->deleteProductCategory($id);

        return $this->json(null);
    }
}
