<?php

namespace App\Service;

use App\Attribute\RequestBody;
use App\Entity\Product;
use App\Exception\ProductAlreadyExistException;
use App\Model\Editor\ActivateProductRequest;
use App\Model\Editor\ProductCreateRequest;
use App\Model\Editor\ProductUpdateRequest;
use App\Model\Editor\ProductUpsertRequestInterface;
use App\Model\Editor\UploadCoverResponse;
use App\Model\IdResponse;
use App\Model\ProductListItem;
use App\Repository\ProductCategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class EditorProductService
{
    public function __construct(
        private readonly ProductRepository         $productRepository,
        private readonly ProductCategoryRepository $productCategoryRepository,
        private readonly SluggerInterface          $slugger,
        private readonly UploadService             $uploadService,
    ) {
    }

    public function changeActivity(int $id, #[RequestBody] ActivateProductRequest $activateProductRequest): void
    {
        $product = $this->productRepository->getProductById($id);

        $product->setActive($activateProductRequest->isActive());

        $this->productRepository->save($product, true);
    }

    public function createProduct(#[RequestBody] ProductCreateRequest $request): IdResponse
    {
        $product = new Product();

        return new IdResponse($this->upsertProduct($product, $request));
    }

    public function updateProduct($id, #[RequestBody] ProductUpdateRequest $request): IdResponse
    {
        $product = $this->productRepository->getProductById($id);

        return new IdResponse($this->upsertProduct($product, $request));
    }

    private function upsertProduct(Product $product, ProductUpsertRequestInterface $request): int
    {
        if ($request->getTitle()) {
            $slug = $this->slugger->slug($request->getTitle());

            if ($this->productRepository->existBySlug($slug)) {
                throw new ProductAlreadyExistException();
            }
        } else {
            $slug = $product->getSlug();
        }

        $productCategory = $this->productCategoryRepository->getProductCategoryByTitle($request->getProductCategory());

        $product->setTitle($request->getTitle() ?? $product->getTitle())
            ->setDescription($request->getDescription() ?? $product->getDescription())
            ->setAmount($request->getAmount() ?? $product->getAmount())
            ->setPrice($request->getPrice() ?? $product->getPrice())
            ->setProductCategory($productCategory ?? $product->getProductCategory())
            ->setSlug($slug)
            ->setImage($request->getImage() ?? $product->getImage())
            ->setMadeIn($request->getMadeIn() ?? $product->getMadeIn())
            ->setActive($request->isActive() ?? $product->isActive())
            ->setSearchQueries($request->getSearchQueries() ?? $product->getSearchQueries());

        $this->productRepository->save($product, true);

        return $product->getId();
    }

    public function deleteProduct(int $id): void
    {
        $product = $this->productRepository->getProductById($id);

        $this->productRepository->remove($product, true);
    }

    public function uploadCover(int $id, UploadedFile $file): void
    {
        $product = $this->productRepository->getProductById($id);

        $oldImage = $product->getImage();

        $link = $this->uploadService->uploadFile($product, $file);

        $product->setImage($link);

        $this->productRepository->save($product, true);

        if (null !== $product->getImage()) {
            $this->uploadService->deleteProductFile($product->getId(), basename($oldImage));
        }
    }

    public function getProductById(int $productId): ProductListItem
    {
        $product = $this->productRepository->getProductById($productId);

        return $this->map($product);
    }

    private function map(Product $product): ProductListItem
    {
        return (new ProductListItem())
            ->setId($product->getId())
            ->setDescription($product->getDescription())
            ->setTitle($product->getTitle())
            ->setAmount($product->getAmount())
            ->setPrice($product->getPrice())
            ->setProductCategory($product->getProductCategory()?->getTitle())
            ->setSlug($product->getSlug())
            ->setImage($product->getImage())
            ->setMadeIn($product->getMadeIn())
            ->setCreatedAt($product->getCreatedAt()->getTimestamp())
            ->setActive($product->isActive())
            ->setSearchQueries($product->getSearchQueries());
    }
}
