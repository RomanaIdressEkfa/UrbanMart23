<?php

namespace App\Http\Middleware;

use Closure;

class HttpsProtocol {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Only enforce HTTPS when explicitly enabled and not on localhost
        if (env('FORCE_HTTPS') == "On") {
            $host = $request->getHost();
            $isLocal = in_array($host, ['localhost', '127.0.0.1']) || preg_match('/^127\.0\.0\.1$/', $host);
            if (!$isLocal && !$request->secure()) {
                // Preserve full URL including query string
                $fullUrl = $request->fullUrl();
                // Convert http to https safely
                $secureUrl = preg_replace('/^http:/i', 'https:', $fullUrl);
                return redirect()->to($secureUrl);
            }
        }
        return $next($request);
    }
}
