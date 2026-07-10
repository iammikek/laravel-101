<?php

use App\Exceptions\CategoryInUseException;
use App\Exceptions\CategoryNameExistsException;
use App\Exceptions\CategoryNotFoundException;
use App\Exceptions\ItemNotFoundException;
use App\Exceptions\UserEmailExistsException;
use App\Http\Controllers\HealthController;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        apiPrefix: '',
        commands: __DIR__.'/../routes/console.php',
        health: false,
        then: function () {
            Route::get('/', [HealthController::class, 'root']);
            Route::get('/health', [HealthController::class, 'health']);
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(fn (Request $request) => $request->is('shop*') ? route('shop.login') : null);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ItemNotFoundException $e, Request $request) {
            if ($request->is('shop*')) {
                abort(404);
            }

            return response()->json(['detail' => 'Item not found', 'code' => 'ITEM_NOT_FOUND'], 404);
        });

        $exceptions->render(function (CategoryNotFoundException $e, Request $request) {
            if ($request->is('shop*')) {
                abort(404);
            }

            return response()->json(['detail' => 'Category not found', 'code' => 'CATEGORY_NOT_FOUND'], 404);
        });

        $exceptions->render(function (CategoryInUseException $e) {
            return response()->json([
                'detail' => 'Category has items and cannot be deleted',
                'code' => 'CATEGORY_IN_USE',
            ], 409);
        });

        $exceptions->render(function (CategoryNameExistsException $e) {
            return response()->json([
                'detail' => "Category name '{$e->name}' already exists",
                'code' => 'CATEGORY_NAME_EXISTS',
            ], 409);
        });

        $exceptions->render(function (UserEmailExistsException $e, Request $request) {
            if ($request->is('shop*')) {
                return null;
            }

            return response()->json([
                'detail' => "User email '{$e->email}' already exists",
                'code' => 'USER_EMAIL_EXISTS',
            ], 409);
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('shop*')) {
                return null;
            }

            return response()->json(['detail' => 'Unauthorized'], 401);
        });

        $exceptions->render(function (AccessDeniedHttpException $e) {
            if ($e->getMessage() === 'Rate limit exceeded') {
                return response()->json(['detail' => 'Rate limit exceeded', 'code' => 'RATE_LIMIT_EXCEEDED'], 429);
            }

            return response()->json(['detail' => 'Forbidden'], 403);
        });

        $exceptions->render(function (TooManyRequestsHttpException $e) {
            return response()->json(['detail' => 'Rate limit exceeded', 'code' => 'RATE_LIMIT_EXCEEDED'], 429);
        });
    })->create();
