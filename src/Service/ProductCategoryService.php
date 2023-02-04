<?php

namespace App\Service;

use App\Attribute\RequestBody;
use App\Entity\ProductCategory;
use App\Exception\ProductCategoryAlreadyExistException;
use App\Exception\ProductCategoryNotEmptyException;
use App\Model\IdResponse;
use App\Model\ProductCategoryListResponse;
use App\Model\ProductCategoryUpdateRequest;
use App\ModelItem\ProductCategoryListItem;
use App\Repository\ProductCategoryRepository;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductCategoryService
{
    public function __construct(
        private readonly ProductCategoryRepository $productCategoryRepository,
        private readonly SluggerInterface          $slugger,
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

    public function deleteProductCategory(int $id): void
    {
        $category = $this->productCategoryRepository->getProductCategoryById($id);
        $products = $this->productCategoryRepository->countProductsInCategory($id);
        if ($products > 0) {
            throw new ProductCategoryNotEmptyException($products);
        }

        $this->productCategoryRepository->remove($category, true);
    }

    public function createProductCategory(#[RequestBody] ProductCategoryUpdateRequest $request): IdResponse
    {
        $productCategory = new ProductCategory();

        return new IdResponse($this->upsertCategory($productCategory, $request));
    }

    public function updateProductCategory(int $id, #[RequestBody] ProductCategoryUpdateRequest $request): IdResponse
    {
        $productCategory = $this->productCategoryRepository->getProductCategoryById($id);

        return new IdResponse($this->upsertCategory($productCategory, $request));
    }

    private function upsertCategory(ProductCategory $productCategory, ProductCategoryUpdateRequest $request): int
    {
        $slug = $this->slugger->slug($request->getTitle());
        if ($this->productCategoryRepository->existBySlug($slug)) {
            throw new ProductCategoryAlreadyExistException();
        }

        $productCategory->setTitle($request->getTitle())
                        ->setSlug($slug)
                        ->setImage($request->getImage());

        $this->productCategoryRepository->save($productCategory, true);

        return $productCategory->getId();
    }
}
