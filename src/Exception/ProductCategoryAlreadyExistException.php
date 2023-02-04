<?php

namespace App\Exception;

use RuntimeException;

class ProductCategoryAlreadyExistException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Product category with same title is exist');
    }
}