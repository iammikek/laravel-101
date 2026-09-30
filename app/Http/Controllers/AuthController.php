<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Services\UserService;
use App\Support\ApiSerializer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function __construct(private readonly UserService $userService) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = $this->userService->create($data['email'], $data['password']);

        return response()->json(ApiSerializer::user($user), 201);
    }

    public function login(Request $request): JsonResponse
    {
        $email = (string) $request->input('username', '');
        $password = (string) $request->input('password', '');

        if ($email === '' || $password === '') {
            $payload = $request->json()->all();
            if (is_array($payload)) {
                $email = (string) ($payload['username'] ?? $payload['email'] ?? '');
                $password = (string) ($payload['password'] ?? '');
            }
        }

        $user = $this->userService->authenticate($email, $password);
        if ($user === null) {
            return response()->json(
                ['detail' => 'Incorrect email or password'],
                401,
                ['WWW-Authenticate' => 'Bearer'],
            );
        }

        return response()->json([
            'access_token' => JWTAuth::fromUser($user),
            'token_type' => 'bearer',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = auth('api')->user();
        if ($user === null) {
            return response()->json(['detail' => 'Unauthorized'], 401);
        }

        return response()->json(ApiSerializer::user($user));
    }
}
