<?php

namespace App\Tests;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Helmich\JsonAssert\JsonAssertions;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @internal
 *
 * @coversNothing
 */
abstract class AbstractControllerTest extends WebTestCase
{
    use JsonAssertions;

    protected KernelBrowser $client;

    protected ?EntityManagerInterface $em;

    protected UserPasswordHasherInterface $hasher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->em     = self::getContainer()->get('doctrine.orm.entity_manager');
        $this->hasher = self::getContainer()->get('security.password_hasher');
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->em->close();
        $this->em = null;
    }

    protected function auth(string $email, string $password): void
    {
        $this->client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['email' => $email, 'password' => $password])
        );

        $this->assertResponseIsSuccessful();
        $content = json_decode($this->client->getResponse()->getContent(), true);

        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $content['token']));
    }

    protected function getAdmin(string $email, string $password): User
    {
        return $this->createUser($email, $password, ['ROLE_ADMIN']);
    }

    protected function getEditor(string $email, string $password): User
    {
        return $this->createUser($email, $password, ['ROLE_EDITOR']);
    }

    protected function getUser(string $email, string $password): User
    {
        return $this->createUser($email, $password, ['ROLE_USER']);
    }

    private function createUser(string $email, string $password, array $roles): User
    {
        $user = (new User())
            ->setRoles($roles)
            ->setFirstName($email)
            ->setPhoneNumber($email)
            ->setEmail($email);

        $user->setPassword($this->hasher->hashPassword($user, $password));

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }
}
