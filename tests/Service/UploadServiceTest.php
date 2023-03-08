<?php

namespace App\Tests\Service;

use App\Entity\Product;
use App\Entity\ProductCategory;
use App\Entity\Subscriber;
use App\Exception\UnsupportedEntityTypeException;
use App\Exception\UploadFileInvalidTypeException;
use App\Service\UploadService;
use App\Tests\AbstractTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

class UploadServiceTest extends AbstractTestCase
{
    private Filesystem $filesystem;

    private const UPLOAD_DIR = '/tmp';

    protected function setUp(): void
    {
        parent::setUp();

        $this->filesystem = $this->createMock(Filesystem::class);
    }

    public function testDeleteProductFile(): void
    {
        $this->filesystem->expects($this->once())
            ->method('remove')
            ->with('/tmp/product/1/test.png');

        $this->createService()->deleteProductFile(1, 'test.png');
    }


    public function testDeleteProductCategoryFile(): void
    {
        $this->filesystem->expects($this->once())
            ->method('remove')
            ->with('/tmp/productCategory/1/test.png');

        $this->createService()->deleteProductCategoryFile(1, 'test.png');
    }

    public function testUploadFileInvalidExtension(): void
    {
        $product = $this->createMock(Product::class);
        $this->expectException(UploadFileInvalidTypeException::class);

        $file = $this->createMock(UploadedFile::class);
        $file->expects($this->once())
            ->method('guessExtension')
            ->willReturn(null);

        $this->createService()->uploadFile($product, $file);
    }

    public function testUploadProductFileSuccess(): void
    {
        $product = $this->createMock(Product::class);
        $product->expects($this->once())
            ->method('getId')
            ->willReturn(1);

        $file = $this->createMock(UploadedFile::class);
        $file->expects($this->once())
            ->method('guessExtension')
            ->willReturn('jpg');

        $file->expects($this->once())
            ->method('move')
            ->with($this->equalTo('/tmp/product/1'), $this->callback(function (string $arg) {
                if (!str_ends_with($arg, '.jpg')) {
                    return false;
                }

                return Uuid::isValid(basename($arg, '.jpg'));
            }));

        $actualPath = pathinfo($this->createService()->uploadFile($product, $file));

        $this->assertEquals('/upload/product/1', $actualPath['dirname']);
        $this->assertEquals('jpg', $actualPath['extension']);
        $this->assertTrue(Uuid::isValid($actualPath['filename']));
    }

    public function testUploadProductCategoryFileSuccess(): void
    {
        $product = $this->createMock(ProductCategory::class);
        $product->expects($this->once())
            ->method('getId')
            ->willReturn(1);

        $file = $this->createMock(UploadedFile::class);
        $file->expects($this->once())
            ->method('guessExtension')
            ->willReturn('jpg');

        $file->expects($this->once())
            ->method('move')
            ->with($this->equalTo('/tmp/productCategory/1'), $this->callback(function (string $arg) {
                if (!str_ends_with($arg, '.jpg')) {
                    return false;
                }

                return Uuid::isValid(basename($arg, '.jpg'));
            }));

        $actualPath = pathinfo($this->createService()->uploadFile($product, $file));

        $this->assertEquals('/upload/productCategory/1', $actualPath['dirname']);
        $this->assertEquals('jpg', $actualPath['extension']);
        $this->assertTrue(Uuid::isValid($actualPath['filename']));
    }

    public function testUploadFileInvalidEntity(): void
    {
        $this->expectException(UnsupportedEntityTypeException::class);

        $invalidEntity = $this->createStub(Subscriber::class);
        $invalidEntity->expects($this->once())
            ->method('getId')
            ->willReturn(1);

        $file = $this->createMock(UploadedFile::class);
        $file->expects($this->once())
            ->method('guessExtension')
            ->willReturn('jpg');

        $this->createService()->uploadFile($invalidEntity, $file);
    }

    private function createService(): UploadService
    {
        return new UploadService(self::UPLOAD_DIR, $this->filesystem);
    }
}
