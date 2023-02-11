<?php

namespace App\Model\Editor;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

class ProductCreateRequest
{
    #[NotBlank]
    private string $title;

    #[NotBlank]
    private string $description;

    #[PositiveOrZero]
    private int $amount = 0;

    private float $price = 0;

    private ?string $productCategory = null;

    private ?string $image = null;

    private ?string $madeIn = null;

    private bool $active = false;

    private ?array $searchQueries = null;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getProductCategory(): ?string
    {
        return $this->productCategory;
    }

    public function setProductCategory(?string $productCategory): self
    {
        $this->productCategory = $productCategory;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getMadeIn(): ?string
    {
        return $this->madeIn;
    }

    public function setMadeIn(?string $madeIn): self
    {
        $this->madeIn = $madeIn;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getSearchQueries(): ?array
    {
        return $this->searchQueries;
    }

    /**
     * @param string[]|null $searchQueries
     */
    public function setSearchQueries(?array $searchQueries): self
    {
        $this->searchQueries = $searchQueries;

        return $this;
    }
}
