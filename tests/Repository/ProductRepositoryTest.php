<?php

namespace App\Tests\Repository;

use App\Entity\Product;
use App\Entity\ProductCategory;
use App\Repository\ProductRepository;
use App\Tests\AbstractRepositoryTest;
use DateTime;
use Doctrine\ORM\Exception\ORMException;

/**
 * @internal
 *
 * @coversNothing
 */
class ProductRepositoryTest extends AbstractRepositoryTest
{
    private ProductRepository $productRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productRepository = $this->getRepositoryForEntity(Product::class);
    }

    /**
     * @throws ORMException
     */
    public function testFindByCategoryId()
    {
        $devicesCategory = (new ProductCategory())->setTitle('devices')->setSlug('devices');
        $this->em->persist($devicesCategory);

        for ($i = 0; $i < 5; ++$i) {
            $product = $this->createProduct('device-'.$i, $devicesCategory);
            $this->em->persist($product);
        }

        $this->em->flush();

        $this->assertCount(5, $this->productRepository->findByCategoryId($devicesCategory->getId()));
    }

    private function createProduct(string $string, ProductCategory $devicesCategory): Product
    {
        return (new Product())
            ->setTitle($string)
            ->setDescription($string)
            ->setAmount(2)
            ->setPrice(10)
            ->setProductCategory($devicesCategory)
            ->setSlug($string)
            ->setImage('http://localhost/'.$string.'.png')
            ->setActive(true)
            ->setMadeIn($string)
            ->setCreatedAt(new DateTime('NOW'))
            ->setModifiedAt(new DateTime('NOW'))
        ;
    }
}
