<?php

namespace App\Exceptions;

use RuntimeException;

class UserEmailExistsException extends RuntimeException
{
    public function __construct(public readonly string $email)
    {
        parent::__construct("User email '{$email}' already exists");
    }
}
