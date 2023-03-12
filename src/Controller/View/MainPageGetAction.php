<?php

namespace App\Controller\View;

use App\Model\ErrorResponse;
use App\Service\ProductCategoryService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[
    OA\Response(
        response: 200,
        description: 'Return published product by category',
        content: new Model(type: Response::class)
    ),
    OA\Response(
        response: 404,
        description: 'product category not found',
        content: new Model(type: ErrorResponse::class)
    )
]
#[Route('/', methods: ['GET'])]
class MainPageGetAction extends AbstractController
{
    public function __construct(
        private readonly ProductCategoryService $productCategoryService,
    ) {
    }

    public function __invoke(): Response
    {
        return $this->render('mainPage/mainPage.html.twig', [
            'categories' => $this->productCategoryService->getCategories(),
        ]);
    }
}
