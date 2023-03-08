<?php

namespace App\Exception;

use RuntimeException;

class UnsupportedEntityTypeException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Unsupproted entity type');
    }
}
