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
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdnjs.cloudflare.com https://static.cloudflareinsights.com https://cdn.jsdelivr.net https://www.google.com/recaptcha/api.js https://www.google.com/recaptcha/api2/anchor https://www.google.com/recaptcha/api2/frame https://www.gstatic.com/recaptcha/releases/",
            "style-src 'self' https://cdnjs.cloudflare.com 'unsafe-inline'",
            "img-src 'self' data: https:",
            "font-src 'self' https://cdnjs.cloudflare.com",
            "connect-src 'self' https://cdn.jsdelivr.net http://deon-experimental-dalton.ngrok-free.dev https://deon-experimental-dalton.ngrok-free.dev",
            "frame-ancestors 'none'",
            "upgrade-insecure-requests",
        ]);

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
