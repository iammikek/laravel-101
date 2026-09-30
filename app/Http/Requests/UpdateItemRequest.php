<?php

namespace App\Http\Requests;

class UpdateItemRequest extends ApiFormRequest
{
    /** @return array<string, list<string>> */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'min:1', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'price' => ['sometimes', 'nullable', 'numeric', 'gt:0'],
            'category_id' => ['sometimes', 'nullable', 'integer', 'min:1'],
        ];
    }
}
