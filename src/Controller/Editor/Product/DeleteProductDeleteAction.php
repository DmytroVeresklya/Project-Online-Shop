<?php

namespace App\Controller\Editor\Product;

use App\Model\ErrorResponse;
use App\Service\EditorProductService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Editor API')]
#[
    OA\Response(response: 200, description: 'Remove a product'),
    OA\Response(response: 404, description: 'Product not found', content: new Model(type: ErrorResponse::class)),
]
#[Route(path: '/api/editor/delete/product/{id}', methods: ['DELETE'])]
final class DeleteProductDeleteAction extends AbstractController
{
    public function __construct(
        private readonly EditorProductService $editorProductService,
    ) {
    }

    public function __invoke(int $id): JsonResponse
    {
        $this->editorProductService->deleteProduct($id);

        return $this->json(null);
    }
}
