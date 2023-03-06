<?php

namespace App\Tests\Controller\Editor\ProductCategory;

use App\Entity\ProductCategory;
use App\Repository\ProductCategoryRepository;
use App\Tests\AbstractControllerTest;
use App\Tests\MockUtils;
use Symfony\Component\HttpFoundation\Request;

class ProductCategoryUpdatePutActionTest extends AbstractControllerTest
{
    private ProductCategoryRepository $productCategoryRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productCategoryRepository = $this->getRepository(ProductCategory::class);
    }

    public function testProductCategoryUpdatePostActionSuccess(): void
    {
        $this->getEditor(self::VALID_USER_EMAIL, self::VALID_PASSWORD);
        $this->auth(self::VALID_USER_EMAIL, self::VALID_PASSWORD);

        $this->em->persist($productCategory = MockUtils::createProductCategory());
        $this->em->flush();

        $testTitleName = 'testProductCategoryUpdatePostAction';

        $data = [
            'image' => $testTitleName
        ];

        $this->client->request(
            Request::METHOD_PUT,
            "/api/editor/product/category/{$productCategory->getId()}/update",
            [],
            [],
            [],
            json_encode($data)
        );

        $this->assertResponseIsSuccessful();

        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertEquals($response['id'], $productCategory->getId());

        $productCategoryUpdated = $this->productCategoryRepository->find($productCategory->getId());

        $this->assertEquals($testTitleName, $productCategoryUpdated->getImage());
    }
}
