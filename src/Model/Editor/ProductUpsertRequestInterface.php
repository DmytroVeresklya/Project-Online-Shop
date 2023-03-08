<?php

namespace App\Model\Editor;

interface ProductUpsertRequestInterface
{
    public function getTitle();

    public function getDescription();

    public function getAmount();

    public function setAmount(int $amount);

    public function getPrice();

    public function setPrice(float $price);

    public function getProductCategory();

    public function setProductCategory(?string $productCategory);

    public function getImage();

    public function setImage(?string $image);

    public function getMadeIn();

    public function setMadeIn(?string $madeIn);

    public function isActive();

    public function setActive(bool $active);

    public function getSearchQueries();

    public function setSearchQueries(?array $searchQueries);
}
