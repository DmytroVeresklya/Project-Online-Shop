<?php

namespace App\Controller\Editor\Product;

use App\Attribute\RequestBody;
use App\Model\Editor\ActivateProductRequest;
use App\Model\ErrorResponse;
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
        description: 'Activate/Deactivate a product',
    ),
    OA\Response(
        response: 400,
        description: 'Validation failed',
        content: new Model(type: ErrorResponse::class)
    ),
    OA\RequestBody(content: new Model(type: ActivateProductRequest::class))
]
#[Route(path: '/api/editor/change/product/{id}/activity', methods: ['POST'])]
final class ChangeProductActivityPostAction extends AbstractController
{
    public function __construct(private readonly EditorProductService $editorProductService)
    {
    }

    public function __invoke(int $id, #[RequestBody] ActivateProductRequest $request): JsonResponse
    {
        $this->editorProductService->changeActivity($id, $request);

        return $this->json(null);
    }
}
