<?php

namespace App\Tests\Service;

use App\Entity\Product;
use App\Entity\ProductCategory;
use App\Exception\ProductCategoryNotFoundException;
use App\Model\ProductListResponse;
use App\ModelItem\ProductListItem;
use App\Repository\ProductCategoryRepository;
use App\Repository\ProductRepository;
use App\Service\ProductService;
use App\Tests\AbstractTestCase;
use DateTime;

/**
 * @internal
 *
 * @coversNothing
 */
class ProductServiceTest extends AbstractTestCase
{
    public function testGetProductsByCategoryNotFound(): void
    {
        $productRepository = $this->createMock(ProductRepository::class);
        $productCategoryRepository = $this->createMock(ProductCategoryRepository::class);
        $productCategoryRepository->expects($this->once())
            ->method('existById')
            ->with(13)
            ->willReturn(false)
        ;

        $this->expectException(ProductCategoryNotFoundException::class);

        (new ProductService($productRepository, $productCategoryRepository))->getProductsByCategory(13);
    }

    public function testGetProductsByCategory(): void
    {
        $productRepository = $this->createMock(ProductRepository::class);
        $productCategoryRepository = $this->createMock(ProductCategoryRepository::class);

        $productRepository->expects($this->once())
            ->method('findByCategoryId')
            ->with(13)
            ->willReturn([$this->createProduct()])
        ;

        $productCategoryRepository->expects($this->once())
            ->method('existById')
            ->with(13)
            ->willReturn(true)
        ;

        $service = new ProductService($productRepository, $productCategoryRepository);
        $expected = new ProductListResponse([$this->createProductItemModel()]);

        $this->assertEquals($expected, $service->getProductsByCategory(13));
    }

    private function createProduct(): Product
    {
        $product = (new Product())
            ->setTitle('test Title')
            ->setDescription('test Description')
            ->setAmount(1)
            ->setPrice(400)
            ->setProductCategory((new ProductCategory())->setTitle('test_category_title'))
            ->setSlug('test Slug')
            ->setCreatedAt(new DateTime('NOW'))
            ->setImage('test Image')
            ->setMadeIn('test Made_in')
            ->setActive(false)
            ->setSearchQueries(['test01'])
        ;

        $this->setEntityId($product, 109);

        return $product;
    }

    private function createProductItemModel(): ProductListItem
    {
        return (new ProductListItem())
            ->setId(109)
            ->setTitle('test Title')
            ->setDescription('test Description')
            ->setAmount(1)
            ->setPrice(400)
            ->setProductCategory('test_category_title')
            ->setSlug('test Slug')
            ->setImage('test Image')
            ->setMadeIn('test Made_in');
    }
}
