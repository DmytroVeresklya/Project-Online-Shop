<?php

namespace App\Model\Editor;

use Symfony\Component\Validator\Constraints\NotBlank;

class ProductCategoryCreateRequestUpsertRequest implements ProductCategoryUpsertRequestInterface
{
    #[NotBlank]
    private string $title;

    private ?string $image = null;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

}
