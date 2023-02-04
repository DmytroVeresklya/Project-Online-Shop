<?php

namespace App\Service;

use App\Attribute\RequestBody;
use App\Entity\Product;
use App\Exception\ProductAlreadyExistException;
use App\Model\Editor\ActivateProductRequest;
use App\Model\Editor\ProductUpdateRequest;
use App\Model\Editor\UploadCoverResponse;
use App\Model\IdResponse;
use App\Model\ProductCategoryUpdateRequest;
use App\ModelItem\ProductListItem;
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

    public function createProduct(#[RequestBody] ProductUpdateRequest $request): IdResponse
    {
        $product = new Product();

        return new IdResponse($this->upsertProduct($product, $request));
    }

    public function updateProduct($id, #[RequestBody] ProductUpdateRequest $request): IdResponse
    {
        $product = $this->productRepository->getProductById($id);

        return new IdResponse($this->upsertProduct($product, $request));
    }

    private function upsertProduct(Product $product, ProductUpdateRequest $request): int
    {
        $slug = $this->slugger->slug($request->getTitle());
        if ($this->productRepository->existBySlug($slug)) {
            throw new ProductAlreadyExistException();
        }

        $productCategory = $this->productCategoryRepository->getProductCategoryByTitle($request->getProductCategory());

        $product->setTitle($request->getTitle())
            ->setDescription($request->getDescription())
            ->setAmount($request->getAmount())
            ->setPrice($request->getPrice())
            ->setProductCategory($productCategory)
            ->setSlug($slug)
            ->setImage($request->getImage())
            ->setMadeIn($request->getMadeIn())
            ->setActive($request->isActive())
            ->setSearchQueries($request->getSearchQueries());

        $this->productRepository->save($product, true);

        return $product->getId();
    }

    public function deleteProduct(int $id): void
    {
        $product = $this->productRepository->getProductById($id);

        $this->productRepository->remove($product, true);
    }

    public function uploadCover(int $id, UploadedFile $file): UploadCoverResponse
    {
        $product = $this->productRepository->getProductById($id);

        $oldImage = $product->getImage();

        $link = $this->uploadService->uploadProductFile($id, $file);

        $product->setImage($link);

        $this->productRepository->save($product, true);

        if (null !== $product->getImage()) {
            $this->uploadService->deleteProductFile($product->getId(), basename($oldImage));
        }

        return new UploadCoverResponse($link);
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
            ->setTitle($product->getTitle())
            ->setDescription($product->getDescription())
            ->setAmount($product->getAmount())
            ->setPrice($product->getPrice())
            ->setProductCategory($product->getProductCategory()->getTitle())
            ->setSlug($product->getSlug())
            ->setImage($product->getImage())
            ->setMadeIn($product->getMadeIn())
            ->setCreatedAt($product->getCreatedAt()->getTimestamp())
            ->setActive($product->isActive())
            ->setSearchQueries($product->getSearchQueries());
    }
}
