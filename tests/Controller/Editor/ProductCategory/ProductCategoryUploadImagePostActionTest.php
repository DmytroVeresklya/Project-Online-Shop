<?php

namespace App\Tests\Controller\Editor\ProductCategory;

use App\Controller\Editor\ProductCategory\ProductCategoryUploadImagePostAction;
use App\Entity\ProductCategory;
use App\Tests\AbstractControllerTest;
use App\Tests\ControllerHelperTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class ProductCategoryUploadImagePostActionTest extends AbstractControllerTest
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

        $productCategoryId = ($this->createProductCategory(['image' => 'test']))->getId();

        $image = $this->getImages($this->sizes, $this->fileTypes);
        $file  = new UploadedFile($image['path'], $image['name']);

        $this->client->request(
            Request::METHOD_POST,
            "/api/editor/product/category/{$productCategoryId}/upload/image",
            [],
            ['image' => $file],
        );

        $this->assertResponseIsSuccessful();

        $productCategory = $this->getRepository(ProductCategory::class)->find($productCategoryId);
        $link    = $productCategory->getImage();

        $this->assertFileExists(self::DIR_UPLOAD_FILE . $link);

        list($width, $height) = getimagesize(self::DIR_UPLOAD_FILE . $link);
        $this->assertEquals(450, $width);
        $this->assertEquals(450, $height);

        $this->removeFiles[] = $link;
    }
}
