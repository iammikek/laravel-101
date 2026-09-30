<?php

namespace App\Http\Controllers;

use App\Http\Requests\ListItemsRequest;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Services\ItemService;
use App\Support\ApiSerializer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ItemController extends Controller
{
    public function __construct(private readonly ItemService $itemService) {}

    public function index(ListItemsRequest $request): JsonResponse
    {
        $skip = (int) $request->validated('skip');
        $limit = (int) $request->validated('limit');

        [$rows, $total] = $this->itemService->listItems($skip, $limit, $request->filters());

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

    public function store(StoreItemRequest $request): JsonResponse
    {
        $payload = $request->validated();

        $item = $this->itemService->create(
            $payload['name'],
            $payload['description'] ?? null,
            number_format((float) $payload['price'], 2, '.', ''),
            isset($payload['category_id']) ? (int) $payload['category_id'] : null,
        );

        return response()->json(ApiSerializer::item($item), 201);
    }

    public function update(UpdateItemRequest $request, int $itemId): JsonResponse
    {
        $payload = $request->validated();

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
}
