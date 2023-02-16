<?php

namespace App\Tests\Repository;

use App\Entity\Product;
use App\Entity\ProductCategory;
use App\Exception\ProductNotFoundException;
use App\Repository\ProductRepository;
use App\Tests\AbstractRepositoryTest;
use App\Tests\MockUtils;
use DateTime;
use Doctrine\ORM\Exception\ORMException;

class ProductRepositoryTest extends AbstractRepositoryTest
{
    private ProductRepository $productRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productRepository = $this->getRepositoryForEntity(Product::class);
    }

    public function testFindByCategoryId()
    {
        $devicesCategory = (new ProductCategory())->setTitle('devices')->setSlug('devices');
        $this->em->persist($devicesCategory);

        for ($i = 0; $i < 5; ++$i) {
            $product = MockUtils::createProduct($devicesCategory)
                ->setTitle('devices - '. $i)
                ->setSlug('devices-'. $i)
                ->setActive(true);
            $this->em->persist($product);
        }

        $this->em->flush();

        $this->assertCount(5, $this->productRepository->findByCategoryId($devicesCategory->getId()));
    }

    public function testExistBySlug(): void
    {
        $slug = 'testExistBySlug';

        $this->em->persist(MockUtils::createProduct()->setSlug($slug));
        $this->em->flush();

        $this->assertTrue($this->productRepository->existBySlug($slug));
        $this->assertFalse($this->productRepository->existBySlug($slug . 'SomeText'));
    }

    public function testGetProductById(): void
    {
        $product = MockUtils::createProduct();

        $this->em->persist($product);
        $this->em->flush();

        $receivedProduct = $this->productRepository->getProductById($product->getId());

        $this->assertEquals($product, $receivedProduct);

        $this->expectException(ProductNotFoundException::class);

        $this->productRepository->getProductById(1);
    }
}
