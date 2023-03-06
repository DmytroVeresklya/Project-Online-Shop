<?php

namespace App\Model;

class ProductCategoryListItem
{
    public function __construct(
        private int    $id,
        private string $title,
        private string $slug,
        private ?string $image,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }
}
