<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;
use App\Support\ApiSerializer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function __construct(private readonly CategoryService $categoryService)
    {
    }

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

    public function store(Request $request): JsonResponse
    {
        $payload = $this->decodeJson($request);
        if ($payload instanceof JsonResponse) {
            return $payload;
        }

        $validator = Validator::make($payload, [
            'name' => ['required', 'string', 'min:1', 'max:100'],
            'description' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json(['detail' => $validator->errors()->first()], 422);
        }

        $category = $this->categoryService->create(
            $payload['name'],
            $payload['description'] ?? null,
        );

        return response()->json(ApiSerializer::category($category), 201);
    }

    public function update(Request $request, int $categoryId): JsonResponse
    {
        $payload = $this->decodeJson($request);
        if ($payload instanceof JsonResponse) {
            return $payload;
        }

        $category = $this->categoryService->update($categoryId, $payload);

        return response()->json(ApiSerializer::category($category));
    }

    public function destroy(int $categoryId): Response
    {
        $this->categoryService->delete($categoryId);

        return response()->noContent();
    }

    /** @return array<string, mixed>|JsonResponse */
    private function decodeJson(Request $request): array|JsonResponse
    {
        $payload = $request->json()->all();
        if (! is_array($payload)) {
            return response()->json(['detail' => 'Invalid JSON body'], 422);
        }

        return $payload;
    }
}
