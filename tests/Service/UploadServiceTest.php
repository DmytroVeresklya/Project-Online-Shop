<?php

namespace App\Tests\Service;

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

    public function testUploadProductFileInvalidExtension(): void
    {
        $this->expectException(UploadFileInvalidTypeException::class);

        $file = $this->createMock(UploadedFile::class);
        $file->expects($this->once())
            ->method('guessExtension')
            ->willReturn(null);

        $this->createService()->uploadProductFile(1, $file);
    }

    public function testUploadProductFileSuccess(): void
    {
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

        $actualPath = pathinfo($this->createService()->uploadProductFile(1, $file));

        $this->assertEquals('/upload/product/1', $actualPath['dirname']);
        $this->assertEquals('jpg', $actualPath['extension']);
        $this->assertTrue(Uuid::isValid($actualPath['filename']));
    }

    private function createService(): UploadService
    {
        return new UploadService(self::UPLOAD_DIR, $this->filesystem);
    }
}
