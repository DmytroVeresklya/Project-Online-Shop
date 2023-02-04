<?php

namespace App\Exception;

use RuntimeException;

class ProductAlreadyExistException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('product already exists');
    }
}