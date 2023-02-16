<?php

namespace App\Controller\Editor;

use App\Model\ErrorResponse;
use App\Model\ProductListResponse;
use App\Service\EditorProductService;
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
        description: 'product not found',
        content: new Model(type: ErrorResponse::class)
    )
]
#[Route(path: '/api/editor/product/{id}', methods: 'GET')]
final class ProductGetAction extends AbstractController
{
    public function __construct(
        private readonly EditorProductService $editorProductService,
    ) {
    }

    public function __invoke(int $id): Response
    {
        return $this->json($this->editorProductService->getProductById($id));
    }
}
