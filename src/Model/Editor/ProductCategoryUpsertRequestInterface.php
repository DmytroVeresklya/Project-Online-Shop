<?php

namespace App\Model\Editor;

interface ProductCategoryUpsertRequestInterface
{
    public function getTitle();

    public function setTitle(string $title): self;

    public function getImage(): ?string;

    public function setImage(?string $image): self;
}