<?php

namespace App\Http\Requests;

class RegisterRequest extends ApiFormRequest
{
    /** @return array<string, list<string>> */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'min:5', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:128'],
        ];
    }
}
