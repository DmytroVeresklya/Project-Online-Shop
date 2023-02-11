<?php

namespace App\Controller\ProductCategory;

use App\Attribute\RequestBody;
use App\Model\ErrorResponse;
use App\Model\IdResponse;
use App\Model\ProductCategoryCreateRequest;
use App\Model\ProductCategoryUpdateRequest;
use App\Service\ProductCategoryService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[
    OA\Response(
        response: 200,
        description: 'Create product category',
        content: new Model(type: IdResponse::class)
    ),
    OA\Response(
        response:409,
        description: 'Product category with same title is exist',
        content: new Model(type: ErrorResponse::class)
    ),
    OA\Response(
        response:404,
        description: 'Validation failed',
        content: new Model(type: ErrorResponse::class)
    ),
    OA\RequestBody(content: new Model(type: ProductCategoryCreateRequest::class))
]
#[Route(path: '/api/editor/product/category/create', methods: 'POST')]
final class ProductCategoryCreatePostAction extends AbstractController
{
    public function __construct(
        private readonly ProductCategoryService $productCategoryService,
    ) {
    }

    public function __invoke(#[RequestBody] ProductCategoryCreateRequest $request): JsonResponse
    {
        return $this->json($this->productCategoryService->createProductCategory($request));
    }
}
