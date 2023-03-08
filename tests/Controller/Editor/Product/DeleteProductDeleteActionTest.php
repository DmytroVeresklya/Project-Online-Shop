<?php

namespace App\Tests\Controller\Editor\Product;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Tests\AbstractControllerTest;
use App\Tests\MockUtils;

class DeleteProductDeleteActionTest extends AbstractControllerTest
{
    private ProductRepository $productRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productRepository = $this->getRepository(Product::class);
    }

    public function testDeleteProductDeleteActionSuccess(): void
    {
        $product = MockUtils::createProduct();

        $this->em->persist($product);
        $this->em->flush();

        $this->assertNotNull($this->productRepository->find($product->getId()));

        $this->getEditor(self::VALID_USER_EMAIL, self::VALID_PASSWORD);
        $this->auth(self::VALID_USER_EMAIL, self::VALID_PASSWORD);

        $this->client->request('DELETE', "/api/editor/delete/product/{$product->getId()}");

        $this->assertResponseIsSuccessful();
        $this->assertNull($this->productRepository->find($product->getId()));
    }
}
