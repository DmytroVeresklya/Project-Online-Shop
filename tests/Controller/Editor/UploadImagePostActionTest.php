<?php

namespace App\Tests\Controller\Editor;

use App\Entity\Product;
use App\Tests\AbstractControllerTest;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class UploadImagePostActionTest extends AbstractControllerTest
{
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

        $product = $this->getRepository(Product::class)->findOneBy(['title' => 'Winter overalls for dogs']);

        $image = $this->getImages()[0];
        $file = new UploadedFile($image['path'], $image['name']);

        $this->client->request(
            Request::METHOD_POST,
            "/api/editor/product/{$product->getId()}/upload/image",
            [],
            ['cover' => $file],
        );

        $this->assertResponseIsSuccessful();

        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $link = $responseContent['link'];

        $this->assertFileExists(self::DIR_UPLOAD_FILE . $link);

        $this->removeFiles[] = $link;
    }

    private function getImages(): array
    {
        // todo refactoring!)
        $images = [];
        $tempPath = sys_get_temp_dir();
        $bigfile = imagecreatetruecolor(100, 100);
        foreach ($this->fileTypes as $type) {
            foreach ($this->sizes as $size) {
                $w = $size['width'];
                $h = $size['height'];
                $new = imagescale($bigfile, $w, $h);

                $imgFileName = $type . '_' . $w . 'x' . $h  . '_px.' . $type;
                $img = $tempPath . '/' . $imgFileName;

                match ($type) {
                    'bmp' => imagebmp($new, $img),
                    'gif' => imagegif($new, $img),
                    'jpg' => imagejpeg($new, $img),
                    'png' => imagepng($new, $img),
                };

                list($newW, $newH) = getimagesize($img);

                $imageOptions = [];
                $imageOptions['path'] = $img;
                $imageOptions['name'] = $imgFileName;
                $imageOptions['width'] = $newW;
                $imageOptions['height'] = $newH;
                $imageOptions['format'] = $type;

                $images[] = $imageOptions;
            }
        }

        return $images;
    }
}
