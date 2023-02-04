<?php

namespace App\ModelItem;

class ErrorValidationsDetailsItem
{
    public function __construct(
        private string $field,
        private string $message
    ) {
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
