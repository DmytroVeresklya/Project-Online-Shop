<?php

namespace App\Tests;

use App\Entity\Product;
use App\Entity\ProductCategory;
use App\Entity\User;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
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

    protected const VALID_USER_EMAIL = 'testValidUserEmail@mail.com';

    protected const VALID_PASSWORD   = 'validPassword';

    protected const DIR_UPLOAD_FILE  = 'public';

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
        $user = $this->getRepository(User::class)->findOneBy(['email' => $email]);

        if (null === $user) {
            $user = (new User())
                ->setFirstName($email)
                ->setPhoneNumber($email)
                ->setEmail($email);
        }

        $user->setRoles($roles);
        $user->setPassword($this->hasher->hashPassword($user, $password));

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    protected function createProduct(?array $parameters): Product
    {
        $product = $this->getRepository(Product::class)->findOneBy(['title' => $parameters['title'] ?? null]);

        if (null === $product) {
            $product = new Product();
            $product->setTitle($parameters['title'] ?? 'test');
            $product->setDescription($parameters['description'] ?? 'test');
            $product->setSlug($parameters['slug'] ?? 'test');
        }

        if ($parameters['image']) {
            $product->setImage($parameters['image']);
        }
        if (isset($parameters['productCategory'])) {
            $product->setProductCategory($this->createProductCategory($parameters['productCategory']));
        }
        if (isset($parameters['searchQueries'])) {
            $product->setSearchQueries($parameters['searchQueries']);
        }
        if (isset($parameters['price'])) {
            $product->setPrice($parameters['price']);
        }
        if (isset($parameters['amount'])) {
            $product->setAmount($parameters['amount']);
        }
        if (isset($parameters['madeIn'])) {
            $product->setMadeIn($parameters['madeIn']);
        }
        if (isset($parameters['active'])) {
            $product->setActive($parameters['active']);
        }

        $this->em->persist($product);
        $this->em->flush();

        return $product;
    }

    protected function createProductCategory(array $parameters): ProductCategory
    {
        $productCategory = $this->getRepository(ProductCategory::class)->findOneBy([
            'title' => $parameters['title'] ?? null,
        ]);

        if (null === $productCategory) {
            $productCategory = new ProductCategory();
            $productCategory->setTitle($parameters['title'] ?? 'test');
            $productCategory->setSlug($parameters['slug'] ?? 'test');
        }

        if (isset($parameters['image'])) {
            $productCategory->setImage($parameters['image']);
        }

        $this->em->persist($productCategory);
        $this->em->flush();

        return $productCategory;
    }

    protected function getRepository($entity)
    {
        return $this->em->getRepository($entity);
    }
}
