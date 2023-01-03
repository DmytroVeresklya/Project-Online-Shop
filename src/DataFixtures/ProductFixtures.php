<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\ProductCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture implements DependentFixtureInterface, FixtureDataAwareInterface
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
            ProductCategoryFixtures::class,
        ];
    }

    public function load(ObjectManager $manager)
    {
        $fixtures = $this->getFixturesData();
        if (!$fixtures || !\is_array($fixtures)) {
            return;
        }

        foreach ($fixtures as $fixture) {
            $entity = (new Product())
                ->setTitle($fixture['title'])
                ->setDescription($fixture['description'])
                ->setAmount($fixture['amount'])
                ->setPrice($fixture['price'])
                ->setProductCategory($fixture['product_category']);

            $manager->persist($entity);

            $this->addReference(sprintf('%s_%s', Product::class, $fixture['title']), $entity);
            $manager->flush();

            sleep(1);
        }
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getFixturesData(): array
    {
        return [
            [
                'title' => 'Зимовий комбінезон для собак',
                'description' => 'Зимовий комбінезон для собак WauDog представляє космічний одяг для ваших улюбленців! Утеплений комбінезон для собак зігріє в холод восени та взимку!',
                'amount' => 3,
                'price' => 210.45,
                'product_category' => $this->getReference(sprintf('%s_%s', ProductCategory::class, 'Одяг')),
            ],
            [
                'title' => 'Курточка для собак COLLAR WAUDOG Clothes світловідбивна',
                'description' => 'Мультисезонні світловідбивні курточки з подвійним рядом кнопок, які дають змогу регулювати обсяг, обіцяють стати найяскравішою подією осінньо-зимового сезону',
                'amount' => 2,
                'price' => 1294,
                'product_category' => $this->getReference(sprintf('%s_%s', ProductCategory::class, 'Одяг')),
            ],
        ];
    }
}
