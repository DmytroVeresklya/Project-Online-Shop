<?php

namespace App\ModelItem;

class ProductListItem
{
    private int $id;

    private string $title;

    private string $description;

    private int $amount;

    private float $price;

    private ?string $productCategory;

    private string $slug;

    private ?string $image;

    private ?string $madeIn;

    private int $createdAt;

    private bool $active;

    private ?array $searchQueries;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getTitle(): ?string
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

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

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

    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    public function setCreatedAt(int $createdAt): self
    {
        $this->createdAt = $createdAt;

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
     * @return null|string[]
     */
    public function getSearchQueries(): ?array
    {
        return $this->searchQueries;
    }

    public function setSearchQueries(?array $searchQueries): self
    {
        $this->searchQueries = $searchQueries;

        return $this;
    }
}
