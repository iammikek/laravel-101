<?php

namespace App\Http\Controllers;

use App\Services\ItemService;
use App\Support\ApiSerializer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class ItemController extends Controller
{
    public function __construct(private readonly ItemService $itemService) {}

    public function index(Request $request): JsonResponse
    {
        $skip = (int) $request->query('skip', 0);
        $limit = (int) $request->query('limit', 10);

        $validator = Validator::make([
            'skip' => $skip,
            'limit' => $limit,
            'min_price' => $request->query('min_price'),
            'max_price' => $request->query('max_price'),
            'category_id' => $request->query('category_id'),
            'name_contains' => $request->query('name_contains'),
        ], [
            'skip' => ['integer', 'min:0'],
            'limit' => ['integer', 'min:1', 'max:100'],
            'min_price' => ['nullable', 'numeric', 'gt:0'],
            'max_price' => ['nullable', 'numeric', 'gt:0'],
            'category_id' => ['nullable', 'integer', 'min:1'],
            'name_contains' => ['nullable', 'string', 'min:1', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json(['detail' => $validator->errors()->first()], 422);
        }

        $filters = [];
        if ($request->has('min_price')) {
            $filters['min_price'] = $request->query('min_price');
        }
        if ($request->has('max_price')) {
            $filters['max_price'] = $request->query('max_price');
        }
        if ($request->has('category_id')) {
            $filters['category_id'] = (int) $request->query('category_id');
        }
        if ($request->has('name_contains')) {
            $filters['name_contains'] = $request->query('name_contains');
        }

        [$rows, $total] = $this->itemService->listItems($skip, $limit, $filters);

        return response()->json([
            'items' => $rows->map(fn ($item) => ApiSerializer::item($item))->values()->all(),
            'total' => $total,
            'skip' => $skip,
            'limit' => $limit,
        ]);
    }

    public function stats(): JsonResponse
    {
        return response()->json($this->itemService->getStats());
    }

    public function show(int $itemId): JsonResponse
    {
        return response()->json(ApiSerializer::item($this->itemService->getById($itemId)));
    }

    public function store(Request $request): JsonResponse
    {
        $payload = $this->decodeJson($request);
        if ($payload instanceof JsonResponse) {
            return $payload;
        }

        $validator = Validator::make($payload, [
            'name' => ['required', 'string', 'min:1', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'gt:0'],
            'category_id' => ['nullable', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            return response()->json(['detail' => $validator->errors()->first()], 422);
        }

        $item = $this->itemService->create(
            $payload['name'],
            $payload['description'] ?? null,
            number_format((float) $payload['price'], 2, '.', ''),
            isset($payload['category_id']) ? (int) $payload['category_id'] : null,
        );

        return response()->json(ApiSerializer::item($item), 201);
    }

    public function update(Request $request, int $itemId): JsonResponse
    {
        $payload = $this->decodeJson($request);
        if ($payload instanceof JsonResponse) {
            return $payload;
        }

        if (array_key_exists('price', $payload) && $payload['price'] !== null) {
            $payload['price'] = number_format((float) $payload['price'], 2, '.', '');
        }

        $item = $this->itemService->update($itemId, $payload);

        return response()->json(ApiSerializer::item($item));
    }

    public function destroy(int $itemId): Response
    {
        $this->itemService->delete($itemId);

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
