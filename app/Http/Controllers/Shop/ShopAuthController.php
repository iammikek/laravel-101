<?php

namespace App\Http\Controllers\Shop;

use App\Exceptions\UserEmailExistsException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Shop\ShopLoginRequest;
use App\Http\Requests\Shop\ShopRegisterRequest;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ShopAuthController extends Controller
{
    public function __construct(private readonly UserService $userService) {}

    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('shop.home');
        }

        return view('shop.login');
    }

    public function login(ShopLoginRequest $request): RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('shop.home');
        }

        $credentials = $request->validated();

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->route('shop.home');
        }

        return back()->withErrors(['email' => 'Invalid credentials.'])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('shop.home');
    }

    public function showRegister(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('shop.home');
        }

        return view('shop.register');
    }

    public function register(ShopRegisterRequest $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('shop.home');
        }

        $data = $request->validated();

        try {
            $user = $this->userService->create($data['email'], $data['password']);
        } catch (UserEmailExistsException) {
            return view('shop.register')
                ->withErrors(['email' => 'An account with this email already exists.'])
                ->withInput($request->only('email'));
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('shop.home')->with('success', 'Account created. You are logged in.');
    }
}
