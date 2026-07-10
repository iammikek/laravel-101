<?php

namespace App\Exceptions;

use RuntimeException;

class CategoryNameExistsException extends RuntimeException
{
    public function __construct(public readonly string $name)
    {
        parent::__construct("Category name '{$name}' already exists");
    }
}
