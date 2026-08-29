<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\SystemSetting;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Check if maintenance mode is enabled
        if (!SystemSetting::isMaintenanceActive()) {
            return $next($request);
        }

        // 2. Allow authenticated Super Admins to bypass maintenance completely
        if (auth()->check() && method_exists(auth()->user(), 'isSuperAdmin') && auth()->user()->isSuperAdmin()) {
            return $next($request);
        }

        // 3. Allow Super Admin login routes (/admin/login) so administrators can log in
        if ($request->is('admin/login*')) {
            return $next($request);
        }

        // 4. Allow public index / general landing page only (Explicit user requirement)
        if ($request->is('/') || $request->path() === '' || $request->is('index')) {
            return $next($request);
        }

        // 5. Allow the maintenance page itself to avoid redirect loops
        if ($request->is('maintenance')) {
            return $next($request);
        }

        // 6. Allow public static assets, health checks, and inbound webhooks
        if ($request->is([
            'api/webhooks*',
            'widget.js',
            'storage/*',
            'build/*',
            'css/*',
            'js/*',
            'images/*',
            'favicon.ico',
            'up',
            'logout',
        ])) {
            return $next($request);
        }

        // 7. For JSON/API requests, return 503 Service Unavailable
        if ($request->expectsJson() || $request->is('api/*')) {
            $details = SystemSetting::getMaintenanceDetails();
            return response()->json([
                'maintenance'       => true,
                'title'             => $details['title'],
                'message'           => $details['message'],
                'scheduled_ends_at' => $details['scheduled_ends_at'],
            ], 503);
        }

        // 8. Redirect all other requests (dashboard, live chat, settings, login, register) to /maintenance
        return redirect()->route('maintenance');
    }
}
