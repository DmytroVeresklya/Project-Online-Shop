<?php

namespace App\Controller;

use App\Service\ProductCategoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'app_main_page')]
class MainController extends AbstractController
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
