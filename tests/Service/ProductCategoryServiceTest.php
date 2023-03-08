<?php

namespace App\Tests\Service;

use App\Entity\ProductCategory;
use App\Exception\ProductCategoryNotEmptyException;
use App\Model\Editor\ProductCategoryCreateRequestUpsertRequest;
use App\Model\Editor\ProductCategoryUpdateRequestUpsertRequest;
use App\Model\IdResponse;
use App\Model\ProductCategoryListItem;
use App\Model\ProductCategoryListResponse;
use App\Repository\ProductCategoryRepository;
use App\Service\ProductCategoryService;
use App\Service\UploadService;
use App\Tests\AbstractTestCase;
use App\Tests\MockUtils;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\String\UnicodeString;

/**
 * @internal
 *
 * @coversNothing
 */
class ProductCategoryServiceTest extends AbstractTestCase
{
    private ProductCategoryRepository $productCategoryRepository;

    private SluggerInterface          $slugger;

    private UploadService             $uploadService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productCategoryRepository = $this->createMock(ProductCategoryRepository::class);
        $this->slugger                   = $this->createMock(SluggerInterface::class);
        $this->uploadService             = $this->createMock(UploadService::class);
    }

    public function testGetCategories(): void
    {
        $category = MockUtils::createProductCategory();
        $this->setEntityId($category, 7);

        $this->productCategoryRepository->expects($this->once())
            ->method('findAllSortByTitle')
            ->willReturn([$category]);

        $service = $this->getProductCategoryService();
        $expected = new ProductCategoryListResponse([new ProductCategoryListItem(7, 'testTitle', 'testslug', 'testImage')]);

        $this->assertEquals($expected, $service->getCategories());
    }

    public function testDeleteProductCategoryProductsExist(): void
    {
        $category = MockUtils::createProductCategory();
        $this->setEntityId($category, 7);

        $this->productCategoryRepository->expects($this->once())
            ->method('getProductCategoryById')
            ->with(7)
            ->willReturn($category);

        $this->productCategoryRepository->expects($this->once())
            ->method('countProductsInCategory')
            ->with(7)
            ->willReturn(1);

        $this->expectException(ProductCategoryNotEmptyException::class);

        $this->getProductCategoryService()->deleteProductCategory(7);
    }

    public function testCreateProductCategory(): void
    {
        $request = (new ProductCategoryCreateRequestUpsertRequest())->setTitle('test')->setImage('test');

        $this->slugger->expects($this->once())
            ->method('slug')
            ->with($request->getTitle())
            ->willReturn(new UnicodeString('test'));

        $this->productCategoryRepository->expects($this->once())
            ->method('existBySlug')
            ->with('test')
            ->willReturn(false);

        $exceptedProductCategory = (new ProductCategory())->setTitle('test')->setImage('test')->setSlug('test');

        $this->productCategoryRepository->expects($this->once())
            ->method('save')
            ->with($exceptedProductCategory, true)
            ->will($this->returnCallback(function (ProductCategory $productCategory) {
                $this->setEntityId($productCategory, 12);
            }));

        $this->assertEquals(new IdResponse(12), $this->getProductCategoryService()->createProductCategory($request));
    }

    public function testUpdateProductCategory(): void
    {
        $exceptedProductCategory = (new ProductCategory())->setTitle('test')->setImage('test')->setSlug('test');

        $this->productCategoryRepository->expects($this->once())
            ->method('getProductCategoryById')
            ->with(9)
            ->willReturn($exceptedProductCategory);

        $request = (new ProductCategoryUpdateRequestUpsertRequest())->setImage('test');

        $this->productCategoryRepository->expects($this->once())
            ->method('save')
            ->with($exceptedProductCategory, true)
            ->will($this->returnCallback(function (ProductCategory $productCategory) {
                $this->setEntityId($productCategory, 9);
            }));

        $this->assertEquals(new IdResponse(9), $this->getProductCategoryService()->updateProductCategory(9, $request));
    }

    private function getProductCategoryService(): ProductCategoryService
    {
        return (new ProductCategoryService($this->productCategoryRepository, $this->slugger, $this->uploadService));
    }
}
