<?php

namespace App\Service;

use App\Entity\Product;
use App\Exception\UploadFileInvalidTypeException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

class UploadService
{
    private const LINK_PRODUCT_PATTERN = '/upload/product/%d/%s';

    public function __construct(
        private string     $uploadDir,
        private Filesystem $filesystem
    ) {
    }

    public function uploadProductFile(int $productId, UploadedFile $file): string
    {
        $extension = $file->guessExtension();
        if (null === $extension) {
            throw new UploadFileInvalidTypeException();
        }

        $uniqueName = Uuid::v4()->toRfc4122() . '.' . $extension;

        $file->move($this->getUploadPathForProduct($productId), $uniqueName);

        return sprintf(self::LINK_PRODUCT_PATTERN, $productId, $uniqueName);
    }

    public function deleteProductFile(int $productId, string $filename): void
    {
        $this->filesystem->remove($this->getUploadPathForProduct($productId) . DIRECTORY_SEPARATOR . $filename);
    }

    private function getUploadPathForProduct(int $productId): string
    {
        return $this->uploadDir . DIRECTORY_SEPARATOR . 'product' . DIRECTORY_SEPARATOR . $productId;
    }
}
