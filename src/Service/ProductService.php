<?php

namespace App\Service;

use App\Entity\Product;
use App\Entity\ProductCategory;
use App\Model\ProductCategoryListResponse;
use App\Model\ProductListResponse;
use App\ModelItem\ProductCategoryListItem;
use App\ModelItem\ProductListItem;
use App\Repository\ProductCategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\Criteria;

class ProductService
{
    public function __construct(
        private readonly ProductRepository $productRepository,
    ) {}

    public function getProducts(): ProductListResponse
    {
        $categories = $this->productRepository->findBy([], ['title' => Criteria::ASC]);
        $items = array_map(
            fn (Product $product) => new ProductListItem(
                $product->getId(),
                $product->getTitle(),
                $product->getDescription(),
                $product->getAmount(),
                $product->getPrice(),
                $product->getProductCategory()->getTitle()
            ),
            $categories
        );

        return new ProductListResponse($items);
    }
}