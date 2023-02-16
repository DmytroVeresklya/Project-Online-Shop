<?php

namespace App\DataFixtures;

use App\Entity\ProductCategory;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface, FixtureDataAwareInterface
{
    public function __construct(private readonly UserPasswordHasherInterface $hasher)
    {
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     */
    public function getDependencies(): array
    {
        return [
            AppFixtures::class,
        ];
    }

    public function load(ObjectManager $manager)
    {
        $fixtures = $this->getFixturesData();
        if (!$fixtures || !\is_array($fixtures)) {
            return;
        }

        foreach ($fixtures as $fixture) {
            $entity = (new User())
                ->setEmail($fixture['email'])
                ->setFirstName($fixture['first_name'])
                ->setLastName($fixture['last_name'])
                ->setPhoneNumber($fixture['phone_number'])
                ->setRoles($fixture['roles'])
            ;

            $entity->setPassword(
                $this->hasher->hashPassword($entity, $fixture['password'])
            );

            $manager->persist($entity);

            $this->addReference(sprintf('%s_%s', User::class, $fixture['email']), $entity);
        }

        $manager->flush();
    }

    /**
     * @throws Exception
     */
    public function getFixturesData(): array
    {
        return [
            [
                'email' => 'leonardo.dicaprio@mail.com',
                'password' => 'leonardo.dicaprio@mail.com',
                'first_name' => 'Leonardo',
                'last_name' => 'Dicaprio',
                'phone_number' => '0900000022',
                'roles' => ['ROLE_USER'],
            ],
        ];
    }
}
