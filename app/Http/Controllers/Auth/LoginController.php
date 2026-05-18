<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showForm()
    {
        return view('auth.login');
    }

    public function login(Request $request, CartService $cartService)
    {
        $request->validate([
            'identifier' => 'required|string',
            'password' => 'required|string',
        ]);

        $identifier = $request->identifier;
        $field = str_contains($identifier, '@') ? 'email' : 'username';
        $remember = $request->boolean('remember');

        if (Auth::attempt([$field => $identifier, 'password' => $request->password], $remember)) {
            $request->session()->regenerate();

            if (session()->has('cart_id')) {
                $cartService->mergeGuestCartOnLogin(Auth::user(), session('cart_id'));
            }

            return redirect()->intended('/');
        }

        return back()->withErrors(['identifier' => 'Invalid credentials, please try again.'])
                     ->withInput(['identifier' => $request->identifier]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}
