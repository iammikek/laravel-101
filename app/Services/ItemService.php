<?php

namespace App\Services;

use App\Exceptions\ItemNotFoundException;
use App\Models\Item;
use Illuminate\Database\Eloquent\Builder;

class ItemService
{
    public function __construct(
        private readonly CategoryService $categoryService,
    ) {
    }

    /** @param array<string, mixed> $filters
     * @return array{0: \Illuminate\Support\Collection<int, Item>, 1: int}
     */
    public function listItems(int $skip, int $limit, array $filters = []): array
    {
        $query = Item::query()
            ->with('category')
            ->orderBy('id');

        $this->applyFilters($query, $filters);

        $total = (clone $query)->count();
        $rows = $query->skip($skip)->take($limit)->get();

        return [$rows, $total];
    }

    public function getById(int $itemId): Item
    {
        $item = Item::query()->with('category')->find($itemId);
        if ($item === null) {
            throw new ItemNotFoundException($itemId);
        }

        return $item;
    }

    public function create(string $name, ?string $description, string $price, ?int $categoryId): Item
    {
        $category = null;
        if ($categoryId !== null) {
            $category = $this->categoryService->getById($categoryId);
        }

        $item = Item::query()->create([
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'category_id' => $category?->id,
        ]);

        return $this->getById($item->id);
    }

    /** @param array<string, mixed> $data */
    public function update(int $itemId, array $data): Item
    {
        $item = $this->getById($itemId);

        if (array_key_exists('name', $data) && $data['name'] !== null) {
            $item->name = (string) $data['name'];
        }

        if (array_key_exists('description', $data)) {
            $item->description = $data['description'];
        }

        if (array_key_exists('price', $data) && $data['price'] !== null) {
            $item->price = (string) $data['price'];
        }

        if (array_key_exists('category_id', $data)) {
            if ($data['category_id'] === null) {
                $item->category_id = null;
            } else {
                $item->category_id = $this->categoryService->getById((int) $data['category_id'])->id;
            }
        }

        $item->save();

        return $this->getById($itemId);
    }

    public function delete(int $itemId): void
    {
        $this->getById($itemId)->delete();
    }

    /** @return array<string, mixed> */
    public function getStats(): array
    {
        $total = Item::query()->count();

        if ($total === 0) {
            return [
                'total_items' => 0,
                'average_price' => 0.0,
                'min_price' => null,
                'max_price' => null,
                'uncategorized_count' => 0,
                'by_category' => [],
            ];
        }

        $aggregate = Item::query()
            ->selectRaw('AVG(price) as avg_price, MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        $uncategorizedCount = Item::query()->whereNull('category_id')->count();

        $categoryRows = Item::query()
            ->selectRaw('categories.id as category_id, categories.name as category_name, COUNT(items.id) as item_count, AVG(items.price) as average_price')
            ->join('categories', 'categories.id', '=', 'items.category_id')
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('categories.name')
            ->get();

        $byCategory = $categoryRows->map(static fn ($row) => [
            'category_id' => (int) $row->category_id,
            'category_name' => $row->category_name,
            'item_count' => (int) $row->item_count,
            'average_price' => round((float) $row->average_price, 2),
        ])->values()->all();

        return [
            'total_items' => $total,
            'average_price' => round((float) $aggregate->avg_price, 2),
            'min_price' => round((float) $aggregate->min_price, 2),
            'max_price' => round((float) $aggregate->max_price, 2),
            'uncategorized_count' => $uncategorizedCount,
            'by_category' => $byCategory,
        ];
    }

    /** @param Builder<Item> $query
     * @param array<string, mixed> $filters
     */
    private function applyFilters(Builder $query, array $filters): void
    {
        if (isset($filters['min_price'])) {
            $query->where('price', '>=', (string) $filters['min_price']);
        }

        if (isset($filters['max_price'])) {
            $query->where('price', '<=', (string) $filters['max_price']);
        }

        if (isset($filters['category_id'])) {
            $query->where('category_id', (int) $filters['category_id']);
        }

        if (isset($filters['name_contains'])) {
            $query->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower((string) $filters['name_contains']).'%']);
        }
    }
}
