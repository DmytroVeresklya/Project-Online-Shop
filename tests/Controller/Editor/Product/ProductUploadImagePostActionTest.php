<?php

namespace App\Tests\Controller\Editor\Product;

use App\Entity\Product;
use App\Tests\AbstractControllerTest;
use App\Tests\ControllerHelperTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class ProductUploadImagePostActionTest extends AbstractControllerTest
{
    use ControllerHelperTrait;

    private array $sizes = [[ "width" => 800,"height" => 600]];

    private array $fileTypes = ['jpg'];

    private array $removeFiles = [];

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->removeFiles();
    }

    private function removeFiles(): void
    {
        foreach ($this->removeFiles as $file) {
            unlink(self::DIR_UPLOAD_FILE . $file);
        }
    }

    public function testUploadImagePostActionSuccess(): void
    {
        $this->getEditor(self::VALID_USER_EMAIL, self::VALID_PASSWORD);
        $this->auth(self::VALID_USER_EMAIL, self::VALID_PASSWORD);

        $productId = ($this->createProduct(['image' => 'test']))->getId();

        $image = $this->getImages($this->sizes, $this->fileTypes);
        $file  = new UploadedFile($image['path'], $image['name']);

        $this->client->request(
            Request::METHOD_POST,
            "/api/editor/product/{$productId}/upload/image",
            [],
            ['image' => $file],
        );

        $this->assertResponseIsSuccessful();

        $product = $this->getRepository(Product::class)->find($productId);
        $link    = $product->getImage();

        $this->assertFileExists(self::DIR_UPLOAD_FILE . $link);

        list($width, $height) = getimagesize(self::DIR_UPLOAD_FILE . $link);
        $this->assertEquals($this->sizes[0]['width'], $width);
        $this->assertEquals($this->sizes[0]['height'], $height);

        $this->removeFiles[] = $link;
    }
}
