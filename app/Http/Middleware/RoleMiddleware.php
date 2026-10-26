<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (auth()->user()->role !== $role) {
            abort(403, 'Unauthorized access.');
        }

        // if (auth()->user()->account_status !== 'Active') {
        //     auth()->guard()->logout();
        //     $request->session()->invalidate();
        //     $request->session()->regenerateToken();
        //     return redirect()->route('login')->with('error', 'Your account is not active.');
        // }

        return $next($request);
    }
}
