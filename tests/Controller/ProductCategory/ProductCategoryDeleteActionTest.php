<?php

namespace App\Tests\Controller\ProductCategory;

use App\Entity\ProductCategory;
use App\Exception\ProductCategoryNotEmptyException;
use App\Repository\ProductCategoryRepository;
use App\Tests\AbstractControllerTest;
use App\Tests\MockUtils;

class ProductCategoryDeleteActionTest extends AbstractControllerTest
{
    private ProductCategoryRepository $productCategoryRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productCategoryRepository = $this->getRepository(ProductCategory::class);
    }

    public function testProductCategoryDeleteActionSuccess(): void
    {
        $productCategory = MockUtils::createProductCategory();

        $this->em->persist($productCategory);
        $this->em->flush();

        $this->assertNotNull($this->productCategoryRepository->find($productCategory->getId()));

        $this->getEditor(self::VALID_USER_EMAIL, self::VALID_PASSWORD);
        $this->auth(self::VALID_USER_EMAIL, self::VALID_PASSWORD);

        $this->client->request('DELETE', "/api/editor/product/category/{$productCategory->getId()}/delete");

        $this->assertResponseIsSuccessful();
        $this->assertNull($this->productCategoryRepository->find($productCategory->getId()));
    }

    public function testProductCategoryDeleteActionWithExistProductInCategory(): void
    {
        $productCategory = MockUtils::createProductCategory();
        $this->em->persist($productCategory);

        $this->em->persist(MockUtils::createProduct($productCategory));

        $this->em->flush();

        $this->assertNotNull($this->productCategoryRepository->find($productCategory->getId()));

        $this->getEditor(self::VALID_USER_EMAIL, self::VALID_PASSWORD);
        $this->auth(self::VALID_USER_EMAIL, self::VALID_PASSWORD);

        $this->client->request('DELETE', "/api/editor/product/category/{$productCategory->getId()}/delete");

        $this->assertResponseStatusCodeSame(400);

    }
}
