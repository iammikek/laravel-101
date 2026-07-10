<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class HealthController extends Controller
{
    public function root(): JsonResponse
    {
        return response()->json(['message' => 'Hello from laravel-101']);
    }

    public function health(): JsonResponse
    {
        return response()->json(['status' => 'ok']);
    }
}
