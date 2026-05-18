<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * AdminMiddleware ensures only users with is_admin=true can access admin routes.
 *
 * This replaces the old system where admin pages had ZERO access guards —
 * anyone who found the URL could insert or delete products without logging in.
 */
class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect('/login')->with('error', 'Please log in to continue.');
        }

        if (!auth()->user()->isAdmin()) {
            abort(403, 'Access denied. You are currently logged in as a standard user (' . auth()->user()->email . '). Please click "Logout" in the top-right menu first, then log in using the Admin credentials: email: admin@grocerease.com / password: Admin@1234');
        }

        return $next($request);
    }
}
