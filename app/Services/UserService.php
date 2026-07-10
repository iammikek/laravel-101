<?php

namespace App\Services;

use App\Exceptions\UserEmailExistsException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function getByEmail(string $email): ?User
    {
        return User::query()->where('email', $email)->first();
    }

    public function create(string $email, string $password): User
    {
        if ($this->getByEmail($email) !== null) {
            throw new UserEmailExistsException($email);
        }

        return User::query()->create([
            'email' => $email,
            'password' => $password,
        ]);
    }

    public function authenticate(string $email, string $password): ?User
    {
        $user = $this->getByEmail($email);
        if ($user === null || ! Hash::check($password, $user->password)) {
            return null;
        }

        return $user;
    }
}
