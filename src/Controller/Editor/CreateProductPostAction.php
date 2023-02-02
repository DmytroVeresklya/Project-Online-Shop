<?php

namespace App\Controller\Editor;

use App\Attribute\RequestBody;
use App\Model\Editor\CreateProductRequest;
use App\Model\ErrorResponse;
use App\Model\IdResponse;
use App\Service\EditorService;
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
    OA\RequestBody(content: new Model(type: CreateProductRequest::class))
]
#[Route(path: '/api/editor/create/product', methods: ['POST'])]
class CreateProductPostAction extends AbstractController
{
    public function __construct(
        private readonly EditorService $editorService,
    ) {
    }

    public function __invoke(#[RequestBody] CreateProductRequest $request): JsonResponse
    {
        return $this->json($this->editorService->createProduct($request));
    }
}
