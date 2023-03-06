<?php

namespace App\Tests\Controller\Editor\Product;

use App\Entity\Product;
use App\Tests\AbstractControllerTest;
use App\Tests\MockUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ChangeProductActivityPostActionTest extends AbstractControllerTest
{
    public function testChangeProductActivityPostActionSuccess(): void
    {
        $this->em->persist(
            $product = (MockUtils::createProduct()->setActive(false))
        );
        $this->em->flush();

        $this->getEditor(self::VALID_USER_EMAIL, self::VALID_PASSWORD);
        $this->auth(self::VALID_USER_EMAIL, self::VALID_PASSWORD);

        $data = json_encode(['active' => true]);

        $this->client->request(
            Request::METHOD_POST,
            '/api/editor/change/product/' . $product->getId() . '/activity',
            [],
            [],
            [],
            $data
        );

        $this->assertResponseIsSuccessful();

        $changedProduct = $this->getRepository(Product::class)->find($product->getId());

        $this->assertEquals(true, $changedProduct->isActive());
    }

    public function testChangeProductActivityPostActionAccessDenied(): void
    {
        $this->em->persist(
            $product = (MockUtils::createProduct()->setActive(false))
        );
        $this->em->flush();

        $this->getUser(self::VALID_USER_EMAIL, self::VALID_PASSWORD);
        $this->auth(self::VALID_USER_EMAIL, self::VALID_PASSWORD);

        $data = json_encode(['active' => true]);

        $this->client->request(
            Request::METHOD_POST,
            '/api/editor/change/product/' . $product->getId() . '/activity',
            [],
            [],
            [],
            $data
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        $changedProduct = $this->getRepository(Product::class)->find($product->getId());

        $this->assertEquals(false, $changedProduct->isActive());
    }
}
