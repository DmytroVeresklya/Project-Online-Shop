<?php

namespace App\Controller\ProductCategory;

use App\Model\ProductCategoryListResponse;
use App\Service\ProductCategoryService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Response(
    response: 200,
    description: 'Return product categories',
    content: new Model(type: ProductCategoryListResponse::class)
)]
#[Route(path: '/api/product/category', methods: 'GET')]
final class ProductCategoryGetAction extends AbstractController
{
    public function __construct(
        private readonly ProductCategoryService $productCategoryService,
    ) {
    }

    public function __invoke(): JsonResponse
    {
        return $this->json($this->productCategoryService->getCategories());
    }
}
