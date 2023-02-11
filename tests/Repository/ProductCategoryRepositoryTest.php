<?php

namespace App\Tests\Repository;

use App\Entity\ProductCategory;
use App\Exception\ProductCategoryNotFoundException;
use App\Repository\ProductCategoryRepository;
use App\Tests\AbstractRepositoryTest;
use App\Tests\MockUtils;

class ProductCategoryRepositoryTest extends AbstractRepositoryTest
{
    private ProductCategoryRepository $productCategoryRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productCategoryRepository = $this->em->getRepository(ProductCategory::class);
    }

    public function testFindAllSortByTitle()
    {
        $productB = (new ProductCategory())->setTitle('B')->setSlug('B');
        $productD = (new ProductCategory())->setTitle('D')->setSlug('D');
        $productA = (new ProductCategory())->setTitle('A')->setSlug('A');
        $productC = (new ProductCategory())->setTitle('C')->setSlug('C');

        foreach ([$productB, $productD, $productA, $productC] as $productCategory) {
            $this->em->persist($productCategory);
        }

        $this->em->flush();

        $titles = array_map(
            fn (ProductCategory $productCategory) => $productCategory->getTitle(),
            $this->productCategoryRepository->findAllSortByTitle()
        );

        $this->assertEquals(['A', 'Ammunition', 'B', 'C', 'Clothes', 'D'], $titles);
    }

    public function testExistById(): void
    {
        $this->em->persist($product = (new ProductCategory())->setTitle('test')->setSlug('test'));
        $this->em->flush();

        $this->assertTrue($this->productCategoryRepository->existById($product->getId()));
    }

    public function testGetProductCategoryByTitle(): void
    {
        $title = 'test';
        $product = (new ProductCategory())->setTitle($title)->setSlug('test');

        $this->em->persist($product);
        $this->em->flush();

        $receivedProduct = $this->productCategoryRepository->getProductCategoryByTitle($title);

        $this->assertEquals($product, $receivedProduct);

        $this->assertNull($this->productCategoryRepository->getProductCategoryByTitle(null));
    }

    public function testGetProductCategoryById(): void
    {
        $product = (new ProductCategory())->setTitle('test')->setSlug('test');

        $this->em->persist($product);
        $this->em->flush();

        $receivedProduct = $this->productCategoryRepository->getProductCategoryById($product->getId());

        $this->assertEquals($product, $receivedProduct);

        $this->expectException(ProductCategoryNotFoundException::class);

        $this->productCategoryRepository->getProductCategoryById(1);
    }

    public function testCountProductsInCategory(): void
    {
        $productCategory = MockUtils::createProductCategory()->setTitle('testCountProductsInCategory');
        $this->em->persist($productCategory);

        for ($i = 0; $i < 5; ++$i) {
            $product = MockUtils::createProduct($productCategory)
                ->setTitle('testCountProductsInCategory_' . $i)
                ->setDescription('testCountProductsInCategory_' . $i)
                ->setSlug('testCountProductsInCategory_' . $i);
            $this->em->persist($product);

            $productCategory->addProduct($product);
            $this->em->persist($productCategory);
        }

        $this->em->flush();

        $this->assertEquals(5, $this->productCategoryRepository->countProductsInCategory($productCategory->getId()));
    }

    public function testExistBySlug(): void
    {
        $slug = 'testExistBySlug';

        $this->em->persist(MockUtils::createProductCategory()->setSlug($slug));
        $this->em->flush();

        $this->assertTrue($this->productCategoryRepository->existBySlug($slug));
        $this->assertFalse($this->productCategoryRepository->existBySlug($slug . 'SomeText'));
    }
}
