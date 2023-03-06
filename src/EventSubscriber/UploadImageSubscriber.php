<?php

namespace App\EventSubscriber;

use App\Event\ProductCategoryUploadImagePostEvent;
use App\Event\ProductUploadImagePostEvent;
use App\Service\EditorProductService;
use App\Service\ImageOptimizer;
use App\Service\ProductCategoryService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UploadImageSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly ProductCategoryService $productCategoryService,
        private readonly EditorProductService   $editorProductService,
        private readonly ImageOptimizer         $imageOptimizer,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProductCategoryUploadImagePostEvent::NAME => [
                ['onProductCategoryUploadImage', 10]
            ],
            ProductUploadImagePostEvent::NAME => [
                ['onProductUploadImage', 10]
            ],
        ];
    }

    public function onProductCategoryUploadImage(ProductCategoryUploadImagePostEvent $event): void
    {
        $link = $this->productCategoryService->uploadImage($event->getId(), $event->getFile());
        $this->imageOptimizer->resize($link);
    }

    public function onProductUploadImage(ProductUploadImagePostEvent $event): void
    {
        $this->editorProductService->uploadCover($event->getId(), $event->getFile());
    }
}
