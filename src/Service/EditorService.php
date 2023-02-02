<?php

namespace App\Service;

use App\Entity\Product;
use App\Exception\ProductAlreadyExistException;
use App\Exception\ProductNotFoundException;
use App\Model\Editor\ActivateProductRequest;
use App\Model\Editor\CreateProductRequest;
use App\Model\Editor\UploadCoverResponse;
use App\Model\IdResponse;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class EditorService
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly SluggerInterface  $slugger,
        private readonly UploadService     $uploadService,
    ) {
    }

    public function changeActivity(int $id, ActivateProductRequest $activateProductRequest): void
    {
        $product = $this->productRepository->find($id);
        if (null === $product) {
            throw new ProductNotFoundException();
        }

        $product->setActive($activateProductRequest->isActive());

        $this->productRepository->save($product, true);
    }

    public function createProduct(CreateProductRequest $request): IdResponse
    {
        $slug = $this->slugger->slug($request->getTitle());
        if ($this->productRepository->existBySlug($slug)) {
            throw new ProductAlreadyExistException();
        }

        $product = (new Product())
            ->setTitle($request->getTitle())
            ->setDescription($request->getDescription())
            ->setAmount($request->getAmount())
            ->setPrice($request->getPrice())
            ->setProductCategory($request->getProductCategory())
            ->setSlug($slug)
            ->setImage($request->getImage())
            ->setMadeIn($request->getMadeIn())
            ->setActive($request->isActive())
            ->setSearchQueries($request->getSearchQueries());

        $this->productRepository->save($product, true);

        return new IdResponse($product->getId());
    }

    public function deleteProduct(int $id): void
    {
        $product = $this->productRepository->find($id);

        if (null === $product) {
            throw new ProductNotFoundException();
        }

        $this->productRepository->remove($product, true);
    }

    public function uploadCover(int $id, UploadedFile $file): UploadCoverResponse
    {
        $product = $this->productRepository->find($id);

        if (null === $product) {
            throw new ProductNotFoundException();
        }

        $oldImage = $product->getImage();

        $link = $this->uploadService->uploadProductFile($id, $file);

        $product->setImage($link);

        $this->productRepository->save($product, true);

        if (null !== $product->getImage()) {
            $this->uploadService->deleteProductFile($product->getId(), basename($oldImage));
        }

        return new UploadCoverResponse($link);
    }
}
