<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints\NotBlank;

class ProductCategoryUpdateRequest
{
    private ?string $title = null;

    private ?string $image;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
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
