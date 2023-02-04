<?php

namespace App\Model\Editor;

use Symfony\Component\Validator\Constraints\NotBlank;

class ActivateProductRequest
{
    private bool $active;

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }
}
