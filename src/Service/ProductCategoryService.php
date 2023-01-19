<?php

namespace App\Service;

use App\Entity\ProductCategory;
use App\Model\ProductCategoryListResponse;
use App\ModelItem\ProductCategoryListItem;
use App\Repository\ProductCategoryRepository;
use Doctrine\Common\Collections\Criteria;

class ProductCategoryService
{
    public function __construct(
        private readonly ProductCategoryRepository $productCategoryRepository,
    ) {
    }

    public function getCategories(): ProductCategoryListResponse
    {
        $categories = $this->productCategoryRepository->findAllSortByTitle();
        $items = array_map(
            fn (ProductCategory $productCategory) => new ProductCategoryListItem(
                $productCategory->getId(),
                $productCategory->getTitle(),
                $productCategory->getSlug(),
            ),
            $categories
        );

        return new ProductCategoryListResponse($items);
    }
}