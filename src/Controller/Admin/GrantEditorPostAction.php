<?php

namespace App\Controller\Admin;

use App\Model\ErrorResponse;
use App\Service\Admin\RoleService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Admin API')]
#[
    OA\Response(
        response: 200,
        description: 'Grant EDITOR_ROLE for a user',
    ),
    OA\Response(
        response: 404,
        description: 'User not found',
        content: new Model(type: ErrorResponse::class)
    )
]
#[Route(path: '/api/admin/grantEditor/{userId}', methods: ['POST'])]
final class GrantEditorPostAction extends AbstractController
{
    public function __construct(
        private readonly RoleService $roleService,
    ) {
    }

    public function __invoke(int $userId): Response
    {
        $this->roleService->grantEditor($userId);

        return $this->json(null);
    }
}
