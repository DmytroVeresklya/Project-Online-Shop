<?php

namespace App\Service;

use App\Entity\Product;
use App\Exception\ProductCategoryNotFoundException;
use App\Model\ProductListItem;
use App\Model\ProductListResponse;
use App\Repository\ProductCategoryRepository;
use App\Repository\ProductRepository;

class ProductService
{
    public function __construct(
        private readonly ProductRepository         $productRepository,
        private readonly ProductCategoryRepository $productCategoryRepository
    ) {
    }

    /**
     * @throws ProductCategoryNotFoundException
     */
    public function getProductsByCategory(int $categoryId): ProductListResponse
    {
        if (!$this->productCategoryRepository->existById($categoryId)) {
            throw new ProductCategoryNotFoundException();
        }

        return new ProductListResponse(array_map(
            [$this, 'map'],
            $this->productRepository->findByCategoryId($categoryId)
        ));
    }

    private function map(Product $product): ProductListItem
    {
        return (new ProductListItem())
            ->setId($product->getId())
            ->setTitle($product->getTitle())
            ->setDescription($product->getDescription())
            ->setAmount($product->getAmount())
            ->setPrice($product->getPrice())
            ->setProductCategory($product->getProductCategory()->getTitle())
            ->setSlug($product->getSlug())
            ->setImage($product->getImage())
            ->setMadeIn($product->getMadeIn());
    }
}
