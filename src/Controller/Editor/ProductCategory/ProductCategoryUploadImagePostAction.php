<?php

namespace App\Controller\Editor\ProductCategory;

use App\Attribute\RequestFile;
use App\Event\ProductCategoryUploadImagePostEvent;
use App\Model\ErrorResponse;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotNull;

#[OA\Tag(name: 'Editor API')]
#[
    OA\Response(
        response: 200,
        description: 'Upload image for product category',
    ),
    OA\Response(
        response: 404,
        description: 'Product category not found',
        content: new Model(type: ErrorResponse::class)
    ),
    OA\Response(
        response: 400,
        description: 'Validation Error',
        content: new Model(type: ErrorResponse::class)
    ),
    OA\Response(
        response: 409,
        description: 'Unsupported entity type exception',
        content: new Model(type: ErrorResponse::class)
    ),
]
#[Route(path: '/api/editor/product/category/{id}/upload/image', methods: ['POST'])]
final class ProductCategoryUploadImagePostAction extends AbstractController
{
    public function __construct(private readonly EventDispatcherInterface $eventDispatcher)
    {
    }

    public function __invoke(
        int $id,
        #[RequestFile(field: 'image', constraints: [
        new NotNull(),
        new Image(maxSize: '3M', mimeTypes: ['image/jpeg', 'image/png', 'image/jpg']),
    ])] UploadedFile $file
    ): Response {
        $event = new ProductCategoryUploadImagePostEvent($id, $file);
        $this->eventDispatcher->dispatch($event, ProductCategoryUploadImagePostEvent::NAME);

        return new Response('OK', Response::HTTP_OK);
    }
}
