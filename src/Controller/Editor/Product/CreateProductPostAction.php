<?php

namespace App\Controller\Editor\Product;

use App\Attribute\RequestBody;
use App\Model\Editor\ProductCreateRequest;
use App\Model\ErrorResponse;
use App\Model\IdResponse;
use App\Service\EditorProductService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Editor API')]
#[
    OA\Response(
        response: 200,
        description: 'Create a product',
        content: new Model(type: IdResponse::class)
    ),
    OA\Response(
        response: 400,
        description: 'Validation failed',
        content: new Model(type: ErrorResponse::class)
    ),
    OA\Response(
        response: 409,
        description: 'Product with same title already exist',
        content: new Model(type: ErrorResponse::class)
    ),
    OA\RequestBody(content: new Model(type: ProductCreateRequest::class))
]
#[Route(path: '/api/editor/product/create', methods: ['POST'])]
final class CreateProductPostAction extends AbstractController
{
    public function __construct(
        private readonly EditorProductService $editorProductService,
    ) {
    }

    public function __invoke(#[RequestBody] ProductCreateRequest $request): JsonResponse
    {
        return $this->json($this->editorProductService->createProduct($request));
    }
}
