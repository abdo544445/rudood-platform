<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureWorkspaceIsActive
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Super Admins always bypass workspace status check
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        $workspace = $user->workspace;

        if (!$workspace) {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => 'لا توجد مساحة عمل مرتبطة بهذا الحساب. يرجى التواصل مع الإدارة.',
            ]);
        }

        if ($workspace->status === 'pending') {
            return redirect()->route('subscription.pending')->with([
                'request_email' => $user->email,
                'info'          => 'حسابك ومساحة عمل متجرك قيد المراجعة والاعتماد من قبل مدير النظام (Super Admin). يرجى الانتظار حتى يتم تفعيل الحساب.',
            ]);
        }

        if ($workspace->status === 'suspended' || $workspace->status === 'inactive') {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => 'تم إيقاف هذا الحساب أو مساحة العمل مؤقتاً. يرجى التواصل مع إدارة النظام.',
            ]);
        }

        return $next($request);
    }
}
