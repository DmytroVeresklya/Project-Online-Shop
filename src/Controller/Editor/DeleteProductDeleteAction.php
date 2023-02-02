<?php

namespace App\Controller\Editor;
use App\Model\ErrorResponse;
use App\Service\EditorService;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Editor API')]
#[
    OA\Response(response: 200, description: 'Remove a product'),
    OA\Response(response: 404, description: 'Product not found', content: new Model(type: ErrorResponse::class)),
]
#[Route(path: '/api/editor/delete/product/{id}', methods: ['DELETE'])]
class DeleteProductDeleteAction extends AbstractController
{
    public function __construct(
        private readonly EditorService $editorService,
    ) {
    }

    public function __invoke(int $id): JsonResponse
    {
        $this->editorService->deleteProduct($id);

        return $this->json(null);
    }
}