<?php

namespace App\Exceptions;

use RuntimeException;

class CategoryNotFoundException extends RuntimeException
{
    public function __construct(public readonly int $categoryId)
    {
        parent::__construct("Category {$categoryId} not found");
    }
}
