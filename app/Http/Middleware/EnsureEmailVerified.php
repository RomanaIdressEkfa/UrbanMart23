<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Mohammad Hassan
        // Skip email verification for wholesalers
        if ($request->user() && $request->user()->user_type == 'wholesaler') {
            return $next($request);
        }
        
        if (!$request->user() || !$request->user()->is_verified) {
            return response()->json([
                'success' => false,
                'message' => 'Please verify your email first.'
            ], 403);
        }
        return $next($request);
    }
}
