<?php

namespace App\Service;

use App\Entity\Product;
use App\Entity\ProductCategory;
use App\Exception\UnsupportedEntityTypeException;
use App\Exception\UploadFileInvalidTypeException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

class UploadService
{
    private const LINK_PRODUCT_PATTERN = '/upload/product/%d/%s';

    private const LINK_PRODUCT_CATEGORY_PATTERN = '/upload/productCategory/%d/%s';

    public function __construct(
        private string         $uploadDir,
        private Filesystem     $filesystem,
    ) {
    }

    public function uploadFile($entity, UploadedFile $file): string
    {
        $extension = $file->guessExtension();
        if (null === $extension) {
            throw new UploadFileInvalidTypeException();
        }

        $uniqueName = Uuid::v4()->toRfc4122() . '.' . $extension;

        $id = $entity->getId();

        if ($entity instanceof Product) {
            $linkPattern = self::LINK_PRODUCT_PATTERN;
            $file->move($this->getUploadPathForProduct($id), $uniqueName);
        } elseif ($entity instanceof ProductCategory) {
            $linkPattern = self::LINK_PRODUCT_CATEGORY_PATTERN;
            $file->move($this->getUploadPathForProductCategory($id), $uniqueName);
        } else {
            throw new UnsupportedEntityTypeException();
        }

        return sprintf($linkPattern, $id, $uniqueName);
    }

    public function deleteProductFile(int $productId, string $filename): void
    {
        $this->filesystem->remove($this->getUploadPathForProduct($productId) . DIRECTORY_SEPARATOR . $filename);
    }

    private function getUploadPathForProduct(int $productId): string
    {
        return $this->uploadDir . DIRECTORY_SEPARATOR . 'product' . DIRECTORY_SEPARATOR . $productId;
    }

    public function deleteProductCategoryFile(int $productCategoryId, string $filename): void
    {
        $pathToFile = $this->getUploadPathForProductCategory($productCategoryId) . DIRECTORY_SEPARATOR . $filename;
        $this->filesystem->remove($pathToFile);
    }

    private function getUploadPathForProductCategory(int $productCategoryId): string
    {
        return $this->uploadDir . DIRECTORY_SEPARATOR . 'productCategory' . DIRECTORY_SEPARATOR . $productCategoryId;
    }
}
