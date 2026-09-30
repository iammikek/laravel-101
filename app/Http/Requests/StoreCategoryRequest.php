<?php

namespace App\Http\Requests;

class StoreCategoryRequest extends ApiFormRequest
{
    /** @return array<string, list<string>> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:1', 'max:100'],
            'description' => ['nullable', 'string'],
        ];
    }
}
