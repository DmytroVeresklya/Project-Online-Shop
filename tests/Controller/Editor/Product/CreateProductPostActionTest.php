<?php

namespace App\Tests\Controller\Editor\Product;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Tests\AbstractControllerTest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CreateProductPostActionTest extends AbstractControllerTest
{
    private ProductRepository $productRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productRepository = $this->getRepository(Product::class);
    }

    public function testCreateProductPostActionSuccess(): void
    {
        $this->getEditor(self::VALID_USER_EMAIL, self::VALID_PASSWORD);
        $this->auth(self::VALID_USER_EMAIL, self::VALID_PASSWORD);

        $data = [
            'title' => 'testTitle',
            'description' => 'testDescription',
            'amount' => 1,
            'price' => 231,
            'made_in' => 'China'
        ];

        $this->client->request(
            Request::METHOD_POST,
            '/api/editor/product/create',
            [],
            [],
            [],
            json_encode($data)
        );

        $this->assertResponseIsSuccessful();

        $responseId = json_decode($this->client->getResponse()->getContent(), true);

        $product = $this->productRepository->find($responseId);

        $this->assertNotNull($product);
        $this->assertProductFields($product, $data);
    }

    private function assertProductFields(Product $product, array $data): void
    {
        $this->assertEquals($product->getTitle(), $data['title']);
        $this->assertEquals($product->getDescription(), $data['description']);
        $this->assertEquals($product->getAmount(), $data['amount'] ?? 0);
        $this->assertEquals($product->getPrice(), $data['price'] ?? 0.0);
        $this->assertEquals($product->getProductCategory(), $data['product_category'] ?? null);
        $this->assertEquals($product->getImage(), $data['image'] ?? null);
        $this->assertEquals($product->getMadeIn(), $data['made_in'] ?? null);
        $this->assertEquals($product->isActive(), $data['active'] ?? false);
        $this->assertEquals($product->getSearchQueries(), $data['search_queries'] ?? []);
    }

    public function testCreateProductPostActionAccessDenied(): void
    {
        $this->getUser(self::VALID_USER_EMAIL, self::VALID_PASSWORD);
        $this->auth(self::VALID_USER_EMAIL, self::VALID_PASSWORD);

        $data = json_encode([
            'title' => 'testTitle',
            'description' => 'testDescription',
            'amount' => 1,
            'price' => 231,
            'made_in' => 'China'
        ]);

        $this->client->request(
            Request::METHOD_POST,
            '/api/editor/product/create',
            [],
            [],
            [],
            $data
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        $this->assertNull($this->productRepository->findOneBy(['title' => 'testTitle']));
    }
}
