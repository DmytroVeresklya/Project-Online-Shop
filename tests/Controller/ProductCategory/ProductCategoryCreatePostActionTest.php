<?php

namespace App\Tests\Controller\ProductCategory;

use App\Entity\ProductCategory;
use App\Repository\ProductCategoryRepository;
use App\Tests\AbstractControllerTest;
use Symfony\Component\HttpFoundation\Request;

class ProductCategoryCreatePostActionTest extends AbstractControllerTest
{
    private ProductCategoryRepository $productCategoryRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productCategoryRepository = $this->getRepository(ProductCategory::class);
    }

    public function testProductCategoryCreatePostActionSuccess(): void
    {
        $this->getEditor(self::VALID_USER_EMAIL, self::VALID_PASSWORD);
        $this->auth(self::VALID_USER_EMAIL, self::VALID_PASSWORD);

        $testTitleName = 'testTitleProductCategory';

        $data = [
            'title' => $testTitleName
        ];

        $this->client->request(
            Request::METHOD_POST,
            '/api/editor/product/category/create',
            [],
            [],
            [],
            json_encode($data)
        );

        $this->assertResponseIsSuccessful();


        $responseId = json_decode($this->client->getResponse()->getContent(), true);

        $productCategory = $this->productCategoryRepository->find($responseId);

        $this->assertNotNull($productCategory);
        $this->assertEquals($testTitleName, $productCategory->getTitle());
    }
}
