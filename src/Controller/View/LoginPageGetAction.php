<?php

namespace App\Controller\View;

use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[
    OA\Response(
        response: 200,
        description: 'Return login page',
    )
]
class LoginPageGetAction extends AbstractController
{
    #[Route('/api/login', methods: ['GET'])]
    public function loginGetAction(): Response
    {
        return $this->render('security/login.html.twig');
    }
}
