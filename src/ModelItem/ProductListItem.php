<?php

namespace App\ModelItem;

use App\Entity\ProductCategory;

class ProductListItem
{
    public function __construct(
        private int    $id,
        private string $title,
        private string $description,
        private int    $amount,
        private float  $price,
        private string $productCategory
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getProductCategory(): string
    {
        return $this->productCategory;
    }

}