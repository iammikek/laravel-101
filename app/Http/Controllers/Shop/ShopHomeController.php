<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Services\ItemService;
use Illuminate\View\View;

class ShopHomeController extends Controller
{
    public function __construct(private readonly ItemService $itemService) {}

    public function home(): View
    {
        return view('shop.home', [
            'stats' => $this->itemService->getStats(),
        ]);
    }
}
