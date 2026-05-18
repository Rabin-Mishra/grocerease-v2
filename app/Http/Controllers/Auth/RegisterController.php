<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showForm()
    {
        return view('auth.register');
    }

    public function register(Request $request, CartService $cartService)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:2|max:150',
            'username' => [
                'required', 'string', 'min:3', 'max:100', 'unique:users',
                'regex:/^[a-zA-Z0-9_]+$/'
            ],
            'email' => 'required|email|max:255|unique:users',
            'phone' => ['nullable', 'string', 'regex:/^(\+977)?[9][6-9]\d{8}$/'],
            'password' => [
                'required', 'string', 'min:8', 'confirmed',
                'regex:/^(?=.*[A-Z])(?=.*\d).+$/'
            ],
        ], [
            'username.regex' => 'Username can only contain letters, numbers, and underscores',
            'phone.regex' => 'Enter a valid Nepali mobile number (e.g. 9812345678)',
            'password.regex' => 'Password must have at least one uppercase letter and one number',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => $validated['password'], // Auto-hashed via model cast
        ]);

        if (session()->has('cart_id')) {
            $cartService->mergeGuestCartOnLogin($user, session('cart_id'));
        }

        Auth::login($user);

        return redirect()->intended('/')->with('success', "Welcome to GrocerEase, {$user->name}!");
    }
}
