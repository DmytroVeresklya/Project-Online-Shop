<?php

namespace App\Tests\Service\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Admin\RoleService;
use App\Tests\AbstractTestCase;

class RoleServiceTest extends AbstractTestCase
{
    private UserRepository $userRepository;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user           = new User();
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->userRepository->expects($this->once())
            ->method('getUserById')
            ->with(10)
            ->willReturn($this->user);
    }

    public function testGrantEditor(): void
    {
        $this->createService()->grantAdmin(10);
        $this->assertEquals(['ROLE_ADMIN'], $this->user->getRoles());
    }

    public function testGrantAdmin(): void
    {
        $this->createService()->grantEditor(10);
        $this->assertEquals(['ROLE_EDITOR'], $this->user->getRoles());
    }

    private function createService(): RoleService
    {
        return new RoleService($this->userRepository);
    }
}
