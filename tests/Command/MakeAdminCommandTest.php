<?php

namespace App\Tests\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class MakeAdminCommandTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    private UserRepository $userRepository;

    private const USER_EMAIL = 'leonardo.dicaprio@mail.com';

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager  = $this->getContainer()->get('doctrine')->getManager();

        $this->userRepository = $this->entityManager->getRepository(User::class);
    }

    public function testExecute(): void
    {
        $user = (new User())
            ->setEmail('testUser@test.com')
            ->setPhoneNumber('phone_number_test')
            ->setRoles(['ROLE_USER'])
            ->setPassword(self::USER_EMAIL);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $user = $this->userRepository->findOneBy(['email' => 'testUser@test.com']);

        $this->assertEquals(['ROLE_USER'], $user->getRoles());

        $kernel        = static::createKernel();
        $application   = new Application($kernel);
        $command       = $application->find('app:make-admin');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['user-id' => $user->getId()]);

        $this->entityManager->refresh($user);

        $this->assertEquals(['ROLE_ADMIN'], $user->getRoles());
    }
}
