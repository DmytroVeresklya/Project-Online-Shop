<?php

namespace App\Event;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Contracts\EventDispatcher\Event;

class ProductCategoryUploadImagePostEvent extends Event
{
    public const NAME = 'product.category.upload.image.post';

    public function __construct(private readonly int $id, private readonly UploadedFile $file)
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFile(): UploadedFile
    {
        return $this->file;
    }
}
