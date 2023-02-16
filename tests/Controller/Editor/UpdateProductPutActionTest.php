<?php

namespace App\Tests\Controller\Editor;

use App\Controller\Editor\UpdateProductPutAction;
use App\Entity\Product;
use App\Tests\AbstractControllerTest;
use App\Tests\MockUtils;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class UpdateProductPutActionTest extends AbstractControllerTest
{
    public function testUpdateProductPutActionSuccess(): void
    {
        $productCategory = MockUtils::createProductCategory();
        $this->em->persist($productCategory);

        $product = MockUtils::createProduct($productCategory)->setActive(true)->setSearchQueries(['test11']);
        $this->em->persist($product);

        $this->em->flush();

        $this->getEditor(self::VALID_USER_EMAIL, self::VALID_PASSWORD);
        $this->auth(self::VALID_USER_EMAIL, self::VALID_PASSWORD);

        $data = [
            'description' => 'testUpdateProductDescription',
            'amount' => 123456,
            'price' => 10000,
            'made_in' => 'Germany',
            'search_queries' => []
        ];

        $this->client->request(
            Request::METHOD_PUT,
            "/api/editor/product/{$product->getId()}/update",
            [],
            [],
            [],
            json_encode($data)
        );

        $this->assertResponseIsSuccessful();

        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals($product->getId(), $response['id']);

        $changedProduct = $this->getRepository(Product::class)->find($product->getId());

        $this->assertEquals($data['description'], $changedProduct->getDescription());
        $this->assertEquals($data['amount'], $changedProduct->getAmount());
        $this->assertEquals($data['price'], $changedProduct->getPrice());
        $this->assertEquals($data['made_in'], $changedProduct->getMadeIn());
        $this->assertEquals($data['search_queries'], $changedProduct->getSearchQueries());
    }
}
