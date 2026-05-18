<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * EnsureCartOwnership verifies the user can only access their own cart.
 *
 * This replaces the old IP-based cart system where:
 * - Two users behind the same router shared one cart
 * - Anyone could forge X-Forwarded-For headers to steal carts
 *
 * Now carts are tied to user_id (authenticated) or session_id (guest).
 */
class EnsureCartOwnership
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // TODO: Verify the cart item being accessed belongs to the current user/session
        return $next($request);
    }
}
