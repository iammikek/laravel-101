<?php

namespace App\Exceptions;

use RuntimeException;

class ItemNotFoundException extends RuntimeException
{
    public function __construct(public readonly int $itemId)
    {
        parent::__construct("Item {$itemId} not found");
    }
}
