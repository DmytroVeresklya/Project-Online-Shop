<?php

namespace App\Tests\Controller\Editor\ProductCategory;

use App\Entity\ProductCategory;
use App\Repository\ProductCategoryRepository;
use App\Tests\AbstractControllerTest;
use App\Tests\MockUtils;

class ProductCategoryGetActionTest extends AbstractControllerTest
{
    public function testProductCategoryGetActionSuccess(): void
    {
        $this->em->persist(
            MockUtils::createProductCategory()
        );
        $this->em->flush();

        $this->client->request('GET', '/api/product/category');
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
                        'required' => ['id', 'title', 'slug'],
                        'properties' => [
                            'title' => ['type' => 'string'],
                            'slug' => ['type' => 'string'],
                            'id' => ['type' => 'integer'],
                        ],
                    ],
                ],
            ],
        ]);
    }
}
