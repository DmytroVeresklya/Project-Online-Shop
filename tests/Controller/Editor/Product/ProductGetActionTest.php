<?php

namespace App\Tests\Controller\Editor\Product;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Tests\AbstractControllerTest;
use App\Tests\MockUtils;

class ProductGetActionTest extends AbstractControllerTest
{
    private ProductRepository $productRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productRepository = $this->getRepository(Product::class);
    }

    public function testProductGetActionSuccess(): void
    {
        $productCategory = MockUtils::createProductCategory();
        $this->em->persist($productCategory);

        $product = MockUtils::createProduct($productCategory);
        $this->em->persist($product);

        $this->em->flush();

        $this->getEditor(self::VALID_USER_EMAIL, self::VALID_PASSWORD);
        $this->auth(self::VALID_USER_EMAIL, self::VALID_PASSWORD);

        $this->client->request('GET', "/api/editor/product/{$product->getId()}");
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertResponseIsSuccessful();
        $this->assertJsonDocumentMatchesSchema($response, [
            'type' => 'object',
            'required' => [
                'id',
                'title',
                'description',
                'amount',
                'price',
                'productCategory',
                'slug',
                'createdAt',
                'active',
                'searchQueries'
            ],
            'properties' => [
                'id' => ['type' => 'integer'],
                'title' => ['type' => 'string'],
                'description' => ['type' => 'string'],
                'amount' => ['type' => 'integer'],
                'price' => ['type' => 'number'],
                'productCategory' => ['type' => 'string'],
                'slug' => ['type' => 'string'],
                'createdAt' => ['type' => 'integer'],
                'active' => ['type' => 'boolean'],
                'searchQueries' => [
                    'type' => 'array',
                    'items' => ['type' => 'string'],
                ],
            ],
        ]);
    }
}
