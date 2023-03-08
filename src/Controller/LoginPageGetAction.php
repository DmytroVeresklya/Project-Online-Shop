<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

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
