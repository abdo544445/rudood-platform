<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Workspace;
use App\Models\Bot;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return Auth::user()->isSuperAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect('/dashboard');
        }
        return view('login');
    }

    /**
     * Handle login form submission.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->isSuperAdmin()) {
                return redirect()->route('admin.dashboard')->with('success', 'مرحباً بك في لوحة الإدارة العليا (Super Admin) 👑');
            }

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
        if (Auth::check()) {
            return Auth::user()->isSuperAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect('/dashboard');
        }
        return view('register');
    }

    /**
     * Handle registration form submission.
     * Creates: Workspace → Bot → User (linked together atomically).
     */
    public function register(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'phone'     => 'required|string|max:20',
            'password'  => 'required|string|min:8|confirmed',
        ]);

        $user = DB::transaction(function () use ($request) {
            // 1. Create workspace for this user
            $workspace = Workspace::create([
                'company_name' => $request->full_name . "'s Workspace",
                'status'       => 'active',
            ]);

            // 2. Create the default Bot for this workspace
            Bot::create([
                'workspace_id'  => $workspace->id,
                'name'          => 'مساعد ردود الذكي',
                'system_prompt' => 'أنت مساعد ذكاء اصطناعي مفيد ومهني. رد على أسئلة العملاء بدقة ولطف.',
                'ai_provider'   => 'openai',
                'model_type'    => 'gpt-4o-mini',
                'bot_tone'      => 'friendly',
                'welcome_message' => 'أهلاً بك! 👋 أنا مساعدك الذكي، كيف يمكنني خدمتك اليوم؟',
                'is_active'     => true,
            ]);

            // 3. Create the user linked to this workspace
            return User::create([
                'name'         => $request->full_name,
                'email'        => $request->email,
                'phone'        => $request->phone,
                'password'     => Hash::make($request->password),
                'workspace_id' => $workspace->id,
                'role'         => 'owner',
            ]);
        });

        // 4. Log the user in
        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/dashboard');
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
