<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; // Add this import

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Use Auth::check() instead of auth()->check()
        if (!Auth::check()) {
            abort(401, 'Unauthenticated');
        }
        
        if ($request->user()->role !== $role) {
            abort(403, 'Unauthorized action.');
        }
        
        return $next($request);
    }
}