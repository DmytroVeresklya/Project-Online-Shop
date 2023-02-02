<?php

namespace App\Tests\Controller;

use App\Entity\Product;
use App\Entity\ProductCategory;
use App\Tests\AbstractControllerTest;
use DateTime;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

/**
 * @internal
 *
 * @coversNothing
 */
class ProductControllerTest extends AbstractControllerTest
{
    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function testProductControllerProductsByCategory()
    {
        $category = $this->createCategory();
        $categoryId = $category->getId();
        $this->createProduct($category);


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
                            'createdAt',
                            'active',
                            'searchQueries',
                        ],
                        'properties' => [
                            'id' => ['type' => 'int'],
                            'title' => ['type' => 'string'],
                            'description' => ['type' => 'string'],
                            'amount' => ['type' => 'int'],
                            'price' => ['type' => 'float'],
                            'productCategory' => ['type' => 'string'],
                            'slug' => ['type' => 'string'],
                            'createdAt' => ['type' => 'int'],
                            'active' => ['type' => 'bool'],
                            'searchQueries' => [
                                'type' => 'array',
                                'items' => ['type' => 'string'],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    private function createCategory(): ProductCategory
    {
        $productCategory =
            (new ProductCategory())
                ->setTitle('test_ProductControllerTest')
                ->setSlug('test_ProductControllerTest')
        ;

        $this->em->persist($productCategory);
        $this->em->flush();

        return $productCategory;
    }

    /**
     * @throws ORMException
     */
    private function createProduct(ProductCategory $category): void
    {
        $this->em->persist(
            (new Product())
                ->setTitle('test_ProductControllerTest')
                ->setDescription('test_ProductControllerTest')
                ->setAmount('3')
                ->setPrice('111.11')
                ->setProductCategory($category)
                ->setSlug('test_ProductControllerTest')
                ->setCreatedAt(new DateTime('NOW'))
                ->setActive(true)
                ->setSearchQueries(['test_ProductControllerTest', 'test', '11.23'])
        );
    }
}
