<?php

namespace App\Controller;

use App\Service\ProductCategoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/product/category')]
class ProductCategoryController extends AbstractController
{
    public function __construct(
        private readonly ProductCategoryService $productCategoryService,
    ) {}

    public function __invoke(): JsonResponse
    {
        return $this->json($this->productCategoryService->getCategories());
    }
}