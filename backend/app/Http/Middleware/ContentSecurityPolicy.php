<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentSecurityPolicy
{
    /**
     * Handle an incoming request and attach Security & CSP headers.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $wsUrl = config('services.websocket_url') ?: 'http://localhost:3000';
        $wsHost = parse_url($wsUrl, PHP_URL_HOST) ?? 'localhost';
        $wsPort = parse_url($wsUrl, PHP_URL_PORT) ?? '3000';
        $wsConnect = "http://{$wsHost}:{$wsPort} ws://{$wsHost}:{$wsPort} https://{$wsHost} wss://{$wsHost}";

        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.socket.io https://cdn.jsdelivr.net",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net",
            "font-src 'self' data: https://fonts.gstatic.com https://cdn.jsdelivr.net",
            "connect-src 'self' {$wsConnect} ws: wss: http: https:",
            "img-src 'self' data: https:",
            "frame-ancestors 'self'",
        ]);

        $response->headers->set('Content-Security-Policy', $csp);
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        return $response;
    }
}
