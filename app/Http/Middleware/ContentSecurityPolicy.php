<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentSecurityPolicy
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' https://cdnjs.cloudflare.com https://static.cloudflareinsights.com",
            "style-src 'self' https://cdnjs.cloudflare.com 'unsafe-inline'",
            "img-src 'self' data: https:",
            "font-src 'self' https://cdnjs.cloudflare.com",
            "connect-src 'self'",
            "frame-ancestors 'none'",
            "upgrade-insecure-requests",
        ]);

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
