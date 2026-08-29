<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Workspace;
use App\Models\Bot;
use App\Models\SystemSetting;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin()
    {
        if (SystemSetting::isMaintenanceActive() && (!Auth::check() || !Auth::user()->isSuperAdmin())) {
            return redirect()->route('maintenance');
        }

        if (Auth::check()) {
            return Auth::user()->isSuperAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect('/dashboard');
        }
        return view('login');
    }

    /**
     * Show dedicated Super Admin login page (accessible during maintenance).
     */
    public function showAdminLogin()
    {
        if (Auth::check() && Auth::user()->isSuperAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return view('login');
    }

    /**
     * Handle Super Admin login during maintenance or normal operations.
     */
    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            if ($user->isSuperAdmin()) {
                $request->session()->regenerate();
                return redirect()->route('admin.dashboard')->with('success', 'مرحباً بك في لوحة الإدارة العليا (Super Admin) 👑');
            }

            // Non-superadmins are rejected if logging in through admin portal or in maintenance
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if (SystemSetting::isMaintenanceActive()) {
                return redirect()->route('maintenance')->with('info', 'المنصة في وضع الصيانة حالياً. تسجيل الدخول متاح فقط لمدير النظام.');
            }

            return redirect()->route('login')->withErrors([
                'email' => 'هذه البوابة مخصصة للمشرفين العامين فقط.',
            ])->withInput($request->only('email'));
        }

        return back()->withErrors([
            'email' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة.',
        ])->withInput($request->only('email'));
    }

    /**
     * Handle standard login form submission.
     */
    public function login(Request $request)
    {
        if (SystemSetting::isMaintenanceActive()) {
            return redirect()->route('maintenance')->with('info', 'المنصة في وضع الصيانة حالياً. تسجيل الدخول متاح فقط لمدير النظام.');
        }

        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            if ($user->isSuperAdmin()) {
                $request->session()->regenerate();
                return redirect()->route('admin.dashboard')->with('success', 'مرحباً بك في لوحة الإدارة العليا (Super Admin) 👑');
            }

            $workspace = $user->workspace;
            if ($workspace && $workspace->status === 'pending') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('subscription.pending')->with([
                    'request_email' => $user->email,
                    'info'          => 'حسابك ومساحة عمل متجرك قيد المراجعة والاعتماد من قبل مدير النظام (Super Admin). يرجى الانتظار حتى يتم تفعيل الحساب.',
                ]);
            }

            if ($workspace && in_array($workspace->status, ['suspended', 'inactive'])) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors([
                    'email' => 'تم إيقاف هذا الحساب أو مساحة العمل مؤقتاً. يرجى التواصل مع إدارة النظام.',
                ])->withInput($request->only('email'));
            }

            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة.',
        ])->withInput($request->only('email'));
    }

    /**
     * Show the registration form.
     */
    public function showRegister()
    {
        if (SystemSetting::isMaintenanceActive()) {
            return redirect()->route('maintenance');
        }

        if (Auth::check()) {
            return Auth::user()->isSuperAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect('/dashboard');
        }
        return view('register');
    }

    /**
     * Handle registration form submission.
     * Creates: Workspace (pending) → Bot → User (linked together atomically).
     * Also creates a SubscriberRequest record for admin visibility and approval.
     */
    public function register(Request $request)
    {
        if (SystemSetting::isMaintenanceActive()) {
            return redirect()->route('maintenance');
        }

        $request->validate([
            'full_name' => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'phone'     => 'required|string|max:20',
            'password'  => 'required|string|min:8|confirmed',
        ]);

        $user = DB::transaction(function () use ($request) {
            // 1. Create workspace for this user with pending status
            $workspace = Workspace::create([
                'company_name' => $request->full_name . "'s Store",
                'status'       => 'pending',
            ]);

            // 2. Create the default Bot for this workspace
            Bot::create([
                'workspace_id'    => $workspace->id,
                'name'            => 'مساعد ردود الذكي',
                'system_prompt'   => 'أنت مساعد ذكاء اصطناعي مفيد ومهني. رد على أسئلة العملاء بدقة ولطف.',
                'ai_provider'     => 'gemini',
                'model_type'      => 'gemini-1.5-flash',
                'bot_tone'        => 'friendly',
                'welcome_message' => 'أهلاً بك! 👋 أنا مساعدك الذكي، كيف يمكنني خدمتك اليوم؟',
                'is_active'       => true,
            ]);

            // 3. Create the user linked to this workspace
            $newUser = User::create([
                'name'         => $request->full_name,
                'email'        => $request->email,
                'phone'        => $request->phone,
                'password'     => Hash::make($request->password),
                'workspace_id' => $workspace->id,
                'role'         => 'owner',
            ]);

            // 4. Create a SubscriberRequest record for admin visibility & approval
            \App\Models\SubscriberRequest::create([
                'name'            => $request->full_name,
                'email'           => $request->email,
                'phone'           => $request->phone,
                'company_name'    => $request->full_name . "'s Store",
                'selected_plan'   => 'starter',
                'notes'           => 'طلب تسجيل ذاتي جديد - بانتظار اعتماد الإدارة',
                'status'          => 'pending',
                'created_user_id' => $newUser->id,
            ]);

            return $newUser;
        });

        return redirect()->route('subscription.pending')->with([
            'request_email' => $user->email,
            'success'       => 'تم استلام طلب تسجيل متجرك بنجاح! حسابك الآن قيد المراجعة والاعتماد من قبل مدير النظام.',
        ]);
    }

    /**
     * Show the subscription pending page.
     */
    public function showSubscriptionPending()
    {
        return view('subscription-pending');
    }

    /**
     * Handle public subscription request submission (Client Requirement #2).
     */
    public function submitSubscriptionRequest(Request $request)
    {
        if (SystemSetting::isMaintenanceActive()) {
            return redirect()->route('maintenance');
        }

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255',
            'phone'         => 'required|string|max:30',
            'company_name'  => 'nullable|string|max:255',
            'selected_plan' => 'nullable|string|in:starter,professional,enterprise',
            'notes'         => 'nullable|string|max:1000',
        ]);

        $subRequest = \App\Models\SubscriberRequest::create([
            'name'          => $validated['name'],
            'email'         => $validated['email'],
            'phone'         => $validated['phone'],
            'company_name'  => $validated['company_name'] ?? ($validated['name'] . "'s Store"),
            'selected_plan' => $validated['selected_plan'] ?? 'professional',
            'notes'         => $validated['notes'] ?? null,
            'status'        => 'pending',
        ]);

        return redirect()->route('subscription.pending')->with([
            'request_id'    => $subRequest->id,
            'request_email' => $subRequest->email,
            'success'       => 'تم استلام طلب اشتراكك بنجاح وجاري المراجعة من قبل مدير النظام.',
        ]);
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
