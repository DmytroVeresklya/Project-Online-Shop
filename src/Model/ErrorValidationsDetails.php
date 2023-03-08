<?php

namespace App\Model;

class ErrorValidationsDetails
{
    private array $violations = [];

    public function addViolation(string $field, string $message): void
    {
        $this->violations[] = new ErrorValidationsDetailsItem($field, $message);
    }

    public function getViolations(): array
    {
        return $this->violations;
    }

}