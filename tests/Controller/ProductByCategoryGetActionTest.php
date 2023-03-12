<?php

namespace App\Tests\Controller;

use App\Tests\AbstractControllerTest;
use App\Tests\MockUtils;

class ProductByCategoryGetActionTest extends AbstractControllerTest
{
    public function testProductCategoryGetProductsGetActionSuccess(): void
    {
        $category = MockUtils::createProductCategory();
        $this->em->persist($category);

        $categoryId = $category->getId();

        $product = MockUtils::createProduct($category);
        $this->em->persist($product);

        $this->em->flush();

        $this->client->request('GET', "/api/category/{$categoryId}/products");
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertResponseIsSuccessful();
        $this->assertJsonDocumentMatchesSchema($response, [
            'type' => 'object',
            'required' => ['items'],
            'properties' => [
                'items' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'required' => [
                            'id',
                            'title',
                            'description',
                            'amount',
                            'price',
                            'productCategory',
                            'slug',
                        ],
                        'properties' => [
                            'id' => ['type' => 'integer'],
                            'title' => ['type' => 'string'],
                            'description' => ['type' => 'string'],
                            'amount' => ['type' => 'integer'],
                            'price' => ['type' => 'number'],
                            'productCategory' => ['type' => 'string'],
                            'slug' => ['type' => 'string'],
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function testProductCategoryGetProductsGetActionInvalidId(): void
    {
        $this->client->request('GET', "/api/category/1/products");

        $this->assertResponseStatusCodeSame(404);
    }
}
