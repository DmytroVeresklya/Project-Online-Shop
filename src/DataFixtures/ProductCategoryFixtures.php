<?php

namespace App\DataFixtures;

use App\Entity\ProductCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;

class ProductCategoryFixtures extends Fixture implements DependentFixtureInterface, FixtureDataAwareInterface
{
    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    public function getDependencies()
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
            $entity = (new ProductCategory())
                ->setTitle($fixture['title'])
                ->setSlug($fixture['slug'])
                ->setImage($fixture['image'])
            ;

            $manager->persist($entity);

            $this->addReference(sprintf('%s_%s', ProductCategory::class, $fixture['title']), $entity);
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
                'title' => 'Clothes',
                'slug' => 'Clothes',
                'image' => null,
            ],
            [
                'title' => 'Ammunition',
                'slug' => 'Ammunition',
                'image' => null,
            ],
        ];
    }
}
