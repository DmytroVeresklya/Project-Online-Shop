<?php

namespace App\Service\Admin;

use App\Repository\UserRepository;

class RoleService
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    public function grantAdmin(int $userId): void
    {
        $this->grantRole($userId, 'ROLE_ADMIN');
    }

    public function grantEditor(int $userId): void
    {
        $this->grantRole($userId, 'ROLE_EDITOR');
    }

    private function grantRole(int $userId, string $role): void
    {
        $user = $this->userRepository->getUserForId($userId);
        $user->setRoles([$role]);

        $this->userRepository->save($user, true);
    }
}
