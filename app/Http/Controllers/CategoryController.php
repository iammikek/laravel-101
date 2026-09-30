<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Services\CategoryService;
use App\Support\ApiSerializer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    public function __construct(private readonly CategoryService $categoryService) {}

    public function index(Request $request): JsonResponse
    {
        $skip = max(0, (int) $request->query('skip', 0));
        $limit = min(100, max(1, (int) $request->query('limit', 10)));

        [$rows, $total] = $this->categoryService->listCategories($skip, $limit);

        return response()->json([
            'items' => $rows->map(fn ($row) => ApiSerializer::category($row))->values()->all(),
            'total' => $total,
            'skip' => $skip,
            'limit' => $limit,
        ]);
    }

    public function show(int $categoryId): JsonResponse
    {
        return response()->json(ApiSerializer::category($this->categoryService->getById($categoryId)));
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $payload = $request->validated();

        $category = $this->categoryService->create(
            $payload['name'],
            $payload['description'] ?? null,
        );

        return response()->json(ApiSerializer::category($category), 201);
    }

    public function update(UpdateCategoryRequest $request, int $categoryId): JsonResponse
    {
        $category = $this->categoryService->update($categoryId, $request->validated());

        return response()->json(ApiSerializer::category($category));
    }

    public function destroy(int $categoryId): Response
    {
        $this->categoryService->delete($categoryId);

        return response()->noContent();
    }
}
