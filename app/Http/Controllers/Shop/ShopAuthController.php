<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ShopAuthController extends Controller
{
    public function __construct(private readonly UserService $userService)
    {
    }

    public function login(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('shop.home');
        }

        if ($request->isMethod('post')) {
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);

            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();

                return redirect()->route('shop.home');
            }

            return back()->withErrors(['email' => 'Invalid credentials.'])->onlyInput('email');
        }

        return view('shop.login');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('shop.home');
    }

    public function register(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('shop.home');
        }

        if ($request->isMethod('post')) {
            $data = $request->validate([
                'email' => ['required', 'email', 'max:255'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);

            try {
                $user = $this->userService->create($data['email'], $data['password']);
            } catch (\App\Exceptions\UserEmailExistsException) {
                return view('shop.register')
                    ->withErrors(['email' => 'An account with this email already exists.'])
                    ->withInput($request->only('email'));
            }

            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->route('shop.home')->with('success', 'Account created. You are logged in.');
        }

        return view('shop.register');
    }
}
