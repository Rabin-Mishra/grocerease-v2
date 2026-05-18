<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Services\CartService;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $cartCount = 0;
            try {
                $cartCount = app(CartService::class)->getItemCount(request());
            } catch (\Exception $e) {
                // Default to 0 on any error
            }

            $view->with('cartCount', $cartCount)
                 ->with('authUser', Auth::user());
        });
    }
}
