<?php

namespace App\Services;

use App\Exceptions\CategoryInUseException;
use App\Exceptions\CategoryNameExistsException;
use App\Exceptions\CategoryNotFoundException;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Support\Collection;

class CategoryService
{
    /** @return array{0: Collection<int, Category>, 1: int} */
    public function listCategories(int $skip, int $limit): array
    {
        $query = Category::query()->orderBy('id');

        $total = (clone $query)->count();
        $rows = $query->skip($skip)->take($limit)->get();

        return [$rows, $total];
    }

    public function getById(int $categoryId): Category
    {
        $category = Category::query()->find($categoryId);
        if ($category === null) {
            throw new CategoryNotFoundException($categoryId);
        }

        return $category;
    }

    public function create(string $name, ?string $description): Category
    {
        $this->ensureUniqueName($name);

        return Category::query()->create([
            'name' => $name,
            'description' => $description,
        ]);
    }

    /** @param array<string, mixed> $data */
    public function update(int $categoryId, array $data): Category
    {
        $category = $this->getById($categoryId);

        if (array_key_exists('name', $data) && $data['name'] !== null) {
            $this->ensureUniqueName((string) $data['name'], $categoryId);
            $category->name = (string) $data['name'];
        }

        if (array_key_exists('description', $data)) {
            $category->description = $data['description'];
        }

        $category->save();

        return $category;
    }

    public function delete(int $categoryId): void
    {
        $category = $this->getById($categoryId);

        if (Item::query()->where('category_id', $category->id)->exists()) {
            throw new CategoryInUseException($categoryId);
        }

        $category->delete();
    }

    private function ensureUniqueName(string $name, ?int $excludeId = null): void
    {
        $query = Category::query()->where('name', $name);
        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            throw new CategoryNameExistsException($name);
        }
    }
}
