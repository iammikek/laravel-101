<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\ItemService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopItemController extends Controller
{
    private const PAGE_SIZE = 10;

    public function __construct(private readonly ItemService $itemService) {}

    public function index(Request $request): View
    {
        $filters = [];
        $nameContains = $request->query('name_contains');
        $categoryId = $request->query('category_id');
        $minPrice = $request->query('min_price');
        $maxPrice = $request->query('max_price');

        if (is_string($nameContains) && $nameContains !== '') {
            $filters['name_contains'] = $nameContains;
        }
        if ($categoryId !== null && $categoryId !== '') {
            $filters['category_id'] = (int) $categoryId;
        }
        if ($minPrice !== null && $minPrice !== '') {
            $filters['min_price'] = $minPrice;
        }
        if ($maxPrice !== null && $maxPrice !== '') {
            $filters['max_price'] = $maxPrice;
        }

        $page = max(1, (int) $request->query('page', 1));
        $skip = ($page - 1) * self::PAGE_SIZE;

        [$items, $total] = $this->itemService->listItems($skip, self::PAGE_SIZE, $filters);
        $totalPages = max(1, (int) ceil($total / self::PAGE_SIZE));

        return view('shop.item-list', [
            'items' => $items,
            'totalCount' => $total,
            'page' => $page,
            'totalPages' => $totalPages,
            'categories' => Category::query()->orderBy('name')->get(),
            'filters' => $request->query(),
        ]);
    }

    public function show(int $id): View
    {
        return view('shop.item-detail', [
            'item' => $this->itemService->getById($id),
        ]);
    }

    public function create(Request $request): View|RedirectResponse
    {
        if ($request->isMethod('post')) {
            $data = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'price' => ['required', 'numeric', 'gt:0'],
                'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            ]);

            $item = $this->itemService->create(
                $data['name'],
                $data['description'] ?? null,
                number_format((float) $data['price'], 2, '.', ''),
                isset($data['category_id']) ? (int) $data['category_id'] : null,
            );

            return redirect()
                ->route('shop.items.show', $item->id)
                ->with('success', sprintf('Created "%s".', $item->name));
        }

        return view('shop.item-form', [
            'pageTitle' => 'Add item',
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }
}
