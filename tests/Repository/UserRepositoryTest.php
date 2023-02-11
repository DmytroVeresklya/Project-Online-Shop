<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Exception\UserNotFoundException;
use App\Repository\UserRepository;
use App\Tests\AbstractRepositoryTest;
use App\Tests\MockUtils;

class UserRepositoryTest extends AbstractRepositoryTest
{
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->getRepositoryForEntity(User::class);
    }

    public function testRemove(): void
    {
        $email = 'testRemove@mail.com';
        $user  =  MockUtils::createUser()->setEmail($email)->setPassword('password');

        $this->em->persist($user);
        $this->em->flush();

        $this->assertNotNull($this->userRepository->findOneBy(['email' => $email]));

        $this->userRepository->remove($user, true);

        $this->assertNull($this->userRepository->findOneBy(['email' => $email]));
    }

    public function testExistsByEmail(): void
    {
        $email = 'testExistByEmail@mail.com';

        $this->em->persist(MockUtils::createUser()->setEmail($email)->setPassword('password'));
        $this->em->flush();

        $this->assertTrue($this->userRepository->existsByEmail($email));
        $this->assertFalse($this->userRepository->existsByEmail('NoExistEmail@mail.com'));
    }

    public function testGetUserById(): void
    {
        $user = MockUtils::createUser()->setPassword('password');

        $this->em->persist($user);
        $this->em->flush();

        $receivedUser = $this->userRepository->getUserById($user->getId());

        $this->assertEquals($user, $receivedUser);

        $this->expectException(UserNotFoundException::class);

        $this->userRepository->getUserById(1);
    }
}
