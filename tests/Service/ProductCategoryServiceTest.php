<?php

namespace App\Tests\Service;

use App\Entity\ProductCategory;
use App\Model\ProductCategoryListResponse;
use App\ModelItem\ProductCategoryListItem;
use App\Repository\ProductCategoryRepository;
use App\Service\ProductCategoryService;
use App\Tests\AbstractTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class ProductCategoryServiceTest extends AbstractTestCase
{
    public function testGetCategories(): void
    {
        $category = (new ProductCategory())->setTitle('Тест')->setSlug('Test');
        $this->setEntityId($category, 7);

        $repository = $this->createMock(ProductCategoryRepository::class);
        $repository->expects($this->once())
            ->method('findAllSortByTitle')
            ->willReturn([$category])
        ;

        $service = new ProductCategoryService($repository);
        $expected = new ProductCategoryListResponse([new ProductCategoryListItem(7, 'Тест', 'Test')]);

        $this->assertEquals($expected, $service->getCategories());
    }
}
