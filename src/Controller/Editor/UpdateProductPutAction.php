<?php

namespace App\Controller\Editor;

use App\Attribute\RequestBody;
use App\Model\Editor\ProductUpdateRequest;
use App\Model\ErrorResponse;
use App\Model\IdResponse;
use App\Service\EditorProductService;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

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
        description: 'Product not found',
        content: new Model(type: ErrorResponse::class)
    ),
    OA\Response(
        response: 409,
        description: 'Product already exist',
        content: new Model(type: ErrorResponse::class)
    ),
    OA\RequestBody(content: new Model(type: ProductUpdateRequest::class))
]
#[Route(path: '/api/editor/product/{id}/update', methods: ['PUT'])]
final class UpdateProductPutAction extends AbstractController
{
    public function __construct(
        private readonly EditorProductService $editorProductService,
    ) {
    }

    public function __invoke($id, #[RequestBody] ProductUpdateRequest $request): JsonResponse
    {
        return $this->json($this->editorProductService->updateProduct($id, $request));
    }
}
