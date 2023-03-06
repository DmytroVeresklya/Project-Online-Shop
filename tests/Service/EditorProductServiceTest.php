<?php

namespace App\Tests\Service;

use App\Entity\Product;
use App\Exception\ProductAlreadyExistException;
use App\Model\Editor\ActivateProductRequest;
use App\Model\Editor\ProductCreateRequest;
use App\Model\Editor\ProductUpdateRequest;
use App\Model\Editor\UploadCoverResponse;
use App\Model\IdResponse;
use App\Model\ProductListItem;
use App\Repository\ProductCategoryRepository;
use App\Repository\ProductRepository;
use App\Service\EditorProductService;
use App\Service\UploadService;
use App\Tests\AbstractTestCase;
use App\Tests\MockUtils;
use DateTime;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\String\UnicodeString;

class EditorProductServiceTest extends AbstractTestCase
{
    private ProductRepository $productRepository;

    private ProductCategoryRepository $productCategoryRepository;

    private SluggerInterface $slugger;

    private UploadService $uploadService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productRepository = $this->createMock(ProductRepository::class);

        $this->productCategoryRepository = $this->createMock(ProductCategoryRepository::class);

        $this->slugger = $this->createMock(SluggerInterface::class);

        $this->uploadService = $this->createMock(UploadService::class);
    }

    public function testChangeActivity()
    {
        $product = MockUtils::createProduct()->setActive(false);

        $this->productRepository->expects($this->once())
            ->method('getProductById')
            ->with(123)
            ->willReturn($product);

        $request = (new ActivateProductRequest())->setActive(true);

        $this->createService()->changeActivity(123, $request);

        $this->assertEquals(true, $product->isActive());
    }

    public function testUploadCoverSuccess(): void
    {
        $file = new UploadedFile(path: 'path', originalName: 'field', error: UPLOAD_ERR_NO_FILE, test: true);
        $product = MockUtils::createProduct()->setImage(null);
        $this->setEntityId($product, 1);

        $this->productRepository->expects($this->once())
            ->method('getProductById')
            ->with(1)
            ->willReturn($product);

        $this->productRepository->expects($this->once())
            ->method('save');

        $this->uploadService->expects($this->once())
            ->method('uploadFile')
            ->with($product, $file)
            ->willReturn('http://localhost/test.jpg');

        $this->createService()->uploadCover(1, $file);
        $this->assertEquals('http://localhost/test.jpg', $product->getImage());
    }

    public function testUploadCoverRemoveOld(): void
    {
        $file = new UploadedFile(path: 'path', originalName: 'field', error: UPLOAD_ERR_NO_FILE, test: true);
        $product = MockUtils::createProduct()->setImage('http://localhost/old.png');
        $this->setEntityId($product, 1);

        $this->productRepository->expects($this->once())
            ->method('getProductById')
            ->with(1)
            ->willReturn($product);

        $this->productRepository->expects($this->once())
            ->method('save');

        $this->uploadService->expects($this->once())
            ->method('uploadFile')
            ->with($product, $file)
            ->willReturn('http://localhost/test.jpg');

        $this->uploadService->expects($this->once())
            ->method('deleteProductFile')
            ->with(1, basename($product->getImage()));

        $this->createService()->uploadCover(1, $file);
        $this->assertEquals('http://localhost/test.jpg', $product->getImage());
    }

    public function testGetProductById(): void
    {
        $category = MockUtils::createProductCategory();
        $this->setEntityId($category, 1);

        $product = MockUtils::createProduct($category)
            ->setActive(true)
            ->setCreatedAt(new DateTime('NOW'))
            ->setSearchQueries(['test01']);
        $this->setEntityId($product, 10);

        $productListItem = (new ProductListItem())
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

        $this->productRepository->expects($this->once())
            ->method('getProductById')
            ->with(10)
            ->willReturn($product);

        $this->assertEquals($productListItem, $this->createService()->getProductById(10));
    }

    public function testCreateProduct(): void
    {
        $expectedProduct = (new Product())
            ->setTitle('New Product')
            ->setDescription('test description')
            ->setSlug('new-product');

        $request = (new ProductCreateRequest())
            ->setTitle('New Product')
            ->setDescription('test description');

        $this->slugger->expects($this->once())
            ->method('slug')
            ->with('New Product')
            ->willReturn(new UnicodeString('new-product'));

        $this->productRepository->expects($this->once())
            ->method('existBySlug')
            ->with('new-product')
            ->willReturn(false);

        $this->productCategoryRepository->expects($this->once())
            ->method('getProductCategoryByTitle')
            ->with(null)
            ->willReturn(null);

        $this->productRepository->expects($this->once())
            ->method('save')
            ->with($expectedProduct, true)
            ->will($this->returnCallback(function (Product $product) {
                $this->setEntityId($product, 11);
            }));

        $this->assertEquals(new IdResponse(11), $this->createService()->createProduct($request));
    }

    public function testCreateProductSlugExistsException(): void
    {
        $this->expectException(ProductAlreadyExistException::class);

        $request = (new ProductCreateRequest())
            ->setTitle('New Product')
            ->setDescription('test description');

        $this->slugger->expects($this->once())
            ->method('slug')
            ->with('New Product')
            ->willReturn(new UnicodeString('new-product'));

        $this->productRepository->expects($this->once())
            ->method('existBySlug')
            ->with('new-product')
            ->willReturn(true);


        $this->createService()->createProduct($request);
    }

    public function testUpdateProductSuccessWithTitleRequest(): void
    {
        $expectedProduct = (new Product())
            ->setTitle('New Product')
            ->setDescription('test description')
            ->setSlug('new-product');

        $this->productRepository->expects($this->once())
            ->method('getProductById')
            ->with(11)
            ->willReturn($expectedProduct);

        $request = (new ProductUpdateRequest())
            ->setTitle('New Product')
            ->setDescription('test description')
            ->setPrice('12345');

        $this->slugger->expects($this->once())
            ->method('slug')
            ->with('New Product')
            ->willReturn(new UnicodeString('new-product'));

        $this->productRepository->expects($this->once())
            ->method('existBySlug')
            ->with('new-product')
            ->willReturn(false);

        $this->productCategoryRepository->expects($this->once())
            ->method('getProductCategoryByTitle')
            ->with(null)
            ->willReturn(null);

        $this->productRepository->expects($this->once())
            ->method('save')
            ->with($expectedProduct, true)
            ->will($this->returnCallback(function (Product $product) {
                $this->setEntityId($product, 11);
                $product->setPrice(12345);
            }));

        $this->assertEquals(new IdResponse(11), $this->createService()->updateProduct(11, $request));
    }

    public function testUpdateProductSuccessWithoutTitleRequest(): void
    {
        $expectedProduct = (new Product())
            ->setTitle('New Product')
            ->setDescription('test description')
            ->setSlug('new-product');

        $this->productRepository->expects($this->once())
            ->method('getProductById')
            ->with(11)
            ->willReturn($expectedProduct);

        $request = (new ProductUpdateRequest())
            ->setDescription('test description')
            ->setPrice('12345');

        $this->productCategoryRepository->expects($this->once())
            ->method('getProductCategoryByTitle')
            ->with(null)
            ->willReturn(null);

        $this->productRepository->expects($this->once())
            ->method('save')
            ->with($expectedProduct, true)
            ->will($this->returnCallback(function (Product $product) {
                $this->setEntityId($product, 11);
                $product->setPrice(12345);
            }));

        $this->assertEquals(new IdResponse(11), $this->createService()->updateProduct(11, $request));
    }

    public function testDeleteProduct(): void
    {
        $product = new Product();

        $this->productRepository->expects($this->once())
            ->method('getProductById')
            ->with(1)
            ->willReturn($product);

        $this->productRepository->expects($this->once())
            ->method('remove')
            ->with($product);

        $this->createService()->deleteProduct(1);
    }

    private function createService(): EditorProductService
    {
        return new EditorProductService(
            $this->productRepository,
            $this->productCategoryRepository,
            $this->slugger,
            $this->uploadService
        );
    }
}
