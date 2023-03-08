<?php

namespace App\Service;

use App\Attribute\RequestBody;
use App\Entity\ProductCategory;
use App\Exception\ProductCategoryAlreadyExistException;
use App\Exception\ProductCategoryNotEmptyException;
use App\Model\Editor\ProductCategoryCreateRequestUpsertRequest;
use App\Model\Editor\ProductCategoryUpdateRequestUpsertRequest;
use App\Model\Editor\ProductCategoryUpsertRequestInterface;
use App\Model\Editor\UploadCoverResponse;
use App\Model\IdResponse;
use App\Model\ProductCategoryListItem;
use App\Model\ProductCategoryListResponse;
use App\Repository\ProductCategoryRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductCategoryService
{
    public function __construct(
        private readonly ProductCategoryRepository $productCategoryRepository,
        private readonly SluggerInterface          $slugger,
        private readonly UploadService             $uploadService,
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
                $productCategory->getImage()
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

    public function createProductCategory(#[RequestBody] ProductCategoryCreateRequestUpsertRequest $request): IdResponse
    {
        $productCategory = new ProductCategory();

        return new IdResponse($this->upsertCategory($productCategory, $request));
    }

    public function updateProductCategory(
        int $id,
        #[RequestBody] ProductCategoryUpdateRequestUpsertRequest $request
    ): IdResponse {
        $productCategory = $this->productCategoryRepository->getProductCategoryById($id);

        return new IdResponse($this->upsertCategory($productCategory, $request));
    }

    private function upsertCategory(
        ProductCategory $productCategory,
        ProductCategoryUpsertRequestInterface $request
    ): int {
        if ($request->getTitle()) {
            $slug = $this->slugger->slug($request->getTitle());
            if ($this->productCategoryRepository->existBySlug($slug)) {
                throw new ProductCategoryAlreadyExistException();
            }
        } else {
            $slug = $productCategory->getSlug();
        }

        $productCategory->setTitle($request->getTitle() ?? $productCategory->getTitle())
                        ->setSlug($slug)
                        ->setImage($request->getImage() ?? $productCategory->getImage());

        $this->productCategoryRepository->save($productCategory, true);

        return $productCategory->getId();
    }


    public function uploadImage(int $id, UploadedFile $file): string
    {
        $productCategory = $this->productCategoryRepository->getProductCategoryById($id);

        $oldImage = $productCategory->getImage();

        $link = $this->uploadService->uploadFile($productCategory, $file);

        $productCategory->setImage($link);

        $this->productCategoryRepository->save($productCategory, true);

        if (null !== $productCategory->getImage()) {
            $this->uploadService->deleteProductCategoryFile($productCategory->getId(), basename($oldImage));
        }

        return $link;
    }
}
