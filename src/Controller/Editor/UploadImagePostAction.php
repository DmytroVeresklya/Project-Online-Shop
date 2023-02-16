<?php

namespace App\Controller\Editor;

use App\Attribute\RequestFile;
use App\Model\Editor\UploadCoverResponse;
use App\Model\ErrorResponse;
use App\Service\EditorProductService;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotNull;

#[OA\Tag(name: 'Editor API')]
#[
    OA\Response(
        response: 200,
        description: 'Upload image for product',
        content: new Model(type: UploadCoverResponse::class)
    ),
    OA\Response(
        response: 404,
        description: 'Product not found',
        content: new Model(type: ErrorResponse::class)
    ),
]
#[Route(path: '/api/editor/product/{id}/upload/image', methods: ['POST'])]
final class UploadImagePostAction extends AbstractController
{
    public function __construct(private readonly EditorProductService $editorProductService)
    {
    }

    public function __invoke(
        int $id,
        #[RequestFile(field: 'cover', constraints: [
        new NotNull(),
        new Image(maxSize: '2M', mimeTypes: ['image/jpeg', 'image/png', 'image/jpg']),
    ])] UploadedFile $file
    ): JsonResponse {
        return $this->json($this->editorProductService->uploadCover($id, $file));
    }
}
