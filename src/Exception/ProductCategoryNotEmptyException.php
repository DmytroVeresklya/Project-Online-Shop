<?php

namespace App\Exception;

use RuntimeException;

class ProductCategoryNotEmptyException extends RuntimeException
{
    public function __construct(int $productsCount)
    {
        parent::__construct(sprintf('product category exist %s products', $productsCount));
    }
}
