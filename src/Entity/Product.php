<?php

namespace App\Entity;

use App\EventListener\EntityDateTimeListener;
use App\Repository\ProductRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\EntityListeners([
    EntityDateTimeListener::class,
])]
class Product implements EntityDateTimeAwareInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    #[ORM\SequenceGenerator(sequenceName: 'product_id', allocationSize: 1, initialValue: 100)]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(type: 'string', length: 150, nullable: false)]
    private string $title;

    #[ORM\Column(name: 'description', type: 'string', length: 255, nullable: false)]
    private string $description;

    #[ORM\Column(name: 'amount', type: 'integer', nullable: false)]
    private int $amount = 0;

    #[ORM\Column(name: 'price', nullable: false)]
    private float $price = 0;

    #[ORM\ManyToOne(inversedBy: 'products')]
    private ?ProductCategory $productCategory = null;

    #[ORM\Column(name: 'slug', type: 'string', length: 127, unique: true, nullable: false)]
    private string $slug;

    #[ORM\Column(name: 'image', length: 127, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(name: 'made_in', type: 'string', nullable: true)]
    private ?string $madeIn = null;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private ?DateTime $createdAt = null;

    #[ORM\Column(name: 'modified_at', type: 'datetime')]
    private DateTime $modifiedAt;

    #[ORM\Column(name: 'active', type: 'boolean', options: ['default' => false])]
    private bool $active = false;

    #[ORM\Column(name: 'search_queries', type: 'simple_array', nullable: true)]
    private ?array $searchQueries = null;

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

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

    public function getProductCategory(): ?ProductCategory
    {
        return $this->productCategory;
    }

    public function setProductCategory(?ProductCategory $productCategory): self
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

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getModifiedAt(): DateTime
    {
        return $this->modifiedAt;
    }

    public function setModifiedAt(DateTime $modifiedAt): self
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

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
