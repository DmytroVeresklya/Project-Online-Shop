<?php

namespace App\Tests\Controller;

use App\Entity\ProductCategory;
use App\Tests\AbstractControllerTest;
use Doctrine\ORM\Exception\ORMException;

/**
 *
 * @internal
 *
 * @coversNothing
 */
class ProductCategoryControllerTest extends AbstractControllerTest
{
    /**
     * @throws ORMException
     */
    public function testProductCategoryControllerGetCategories()
    {
        $this->em->persist(
            (new ProductCategory())
                ->setTitle('test')
                ->setSlug('test')
        );

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
                            'id' => ['type' => 'int'],
                        ],
                    ],
                ],
            ],
        ]);
    }
}
