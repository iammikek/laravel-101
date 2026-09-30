<?php

namespace App\Http\Requests;

class ListItemsRequest extends ApiFormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'skip' => (int) $this->query('skip', 0),
            'limit' => (int) $this->query('limit', 10),
        ]);
    }

    /** @return array<string, list<string>> */
    public function rules(): array
    {
        return [
            'skip' => ['integer', 'min:0'],
            'limit' => ['integer', 'min:1', 'max:100'],
            'min_price' => ['nullable', 'numeric', 'gt:0'],
            'max_price' => ['nullable', 'numeric', 'gt:0'],
            'category_id' => ['nullable', 'integer', 'min:1'],
            'name_contains' => ['nullable', 'string', 'min:1', 'max:255'],
        ];
    }

    /** @return array<string, mixed> */
    public function filters(): array
    {
        $filters = [];
        foreach (['min_price', 'max_price', 'name_contains'] as $key) {
            if ($this->query->has($key)) {
                $filters[$key] = $this->query($key);
            }
        }
        if ($this->query->has('category_id')) {
            $filters['category_id'] = (int) $this->query('category_id');
        }

        return $filters;
    }
}
