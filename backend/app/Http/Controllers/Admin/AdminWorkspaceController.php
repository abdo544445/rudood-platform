<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Workspace;
use App\Models\User;
use App\Models\Bot;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminWorkspaceController extends Controller
{
    /**
     * Display a listing of all tenant workspaces / stores.
     */
    public function index(Request $request)
    {
        $query = Workspace::with(['users' => fn($q) => $q->latest()])
            ->withCount(['users', 'bots', 'conversations', 'customers']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhereHas('users', fn($u) => $u->where('email', 'like', "%{$search}%")->orWhere('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('plan_id')) {
            $query->where('plan_id', $request->plan_id);
        }

        $workspaces = $query->latest()->paginate(15)->withQueryString();

        return view('admin.workspaces.index', compact('workspaces'));
    }

    /**
     * Create a new store / client workspace directly from the Super Admin panel.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'plan_id'      => 'required|string|max:50',
            'status'       => 'required|in:active,suspended,trial',
            'owner_name'   => 'required|string|max:255',
            'owner_email'  => 'required|email|max:255|unique:users,email',
            'owner_phone'  => 'nullable|string|max:50',
            'password'     => 'required|string|min:6',
        ]);

        DB::transaction(function () use ($validated) {
            // 1. Create Workspace
            $workspace = Workspace::create([
                'company_name' => $validated['company_name'],
                'plan_id'      => $validated['plan_id'],
                'status'       => $validated['status'],
            ]);

            // 2. Create Owner User
            User::create([
                'workspace_id' => $workspace->id,
                'name'         => $validated['owner_name'],
                'email'        => $validated['owner_email'],
                'phone'        => $validated['owner_phone'] ?? null,
                'role'         => 'owner',
                'password'     => Hash::make($validated['password']),
            ]);

            // 3. Create Default Bot
            Bot::create([
                'workspace_id'  => $workspace->id,
                'name'          => 'المساعد الذكي لـ ' . $validated['company_name'],
                'system_prompt' => 'أنت مساعد ذكاء اصطناعي مفيد ومهني لـ ' . $validated['company_name'] . '، ترد على العملاء بدقة باللغة العربية.',
                'model_type'    => 'gemini-1.5-flash',
                'ai_provider'   => 'gemini',
                'bot_tone'      => 'friendly',
                'temperature'   => 0.7,
                'max_tokens'    => 1000,
                'is_active'     => true,
            ]);

            // 4. Create Subscription Record
            Subscription::create([
                'workspace_id' => $workspace->id,
                'plan_name'    => $validated['plan_id'],
                'price'        => match ($validated['plan_id']) {
                    'pro'        => 49,
                    'enterprise' => 99,
                    default      => 19,
                },
                'status'       => 'active',
                'renews_at'    => now()->addMonth(),
            ]);
        });

        return back()->with('success', "تم إنشاء مساحة العمل وحساب المالك ({$validated['company_name']}) بنجاح ✓");
    }

    /**
     * Display detailed workspace view with bot settings, knowledge base, and team.
     */
    public function show($id)
    {
        $workspace = Workspace::with(['users', 'bots.knowledgeBases', 'bots.autoRules'])
            ->withCount(['conversations', 'customers'])
            ->findOrFail($id);

        $subscription = Subscription::where('workspace_id', $id)->latest()->first();
        $bot = $workspace->bots->first() ?? Bot::firstOrCreate(
            ['workspace_id' => $id],
            [
                'name'          => 'المساعد الذكي لـ ' . $workspace->company_name,
                'system_prompt' => 'أنت مساعد ذكي لـ ' . $workspace->company_name,
                'model_type'    => 'gemini-1.5-flash',
                'ai_provider'   => 'gemini',
                'bot_tone'      => 'friendly',
                'is_active'     => true,
            ]
        );

        return view('admin.workspaces.show', compact('workspace', 'subscription', 'bot'));
    }

    /**
     * Update workspace store information & status.
     */
    public function update(Request $request, $id)
    {
        $workspace = Workspace::findOrFail($id);

        $validated = $request->validate([
            'company_name'         => 'required|string|max:255',
            'plan_id'              => 'required|string|max:50',
            'status'               => 'required|in:active,suspended,trial',
            'allow_custom_api_key' => 'nullable|boolean',
        ]);

        $validated['allow_custom_api_key'] = $request->has('allow_custom_api_key');
        $workspace->update($validated);

        return back()->with('success', "تم تحديث بيانات وسياسات المتجر ({$workspace->company_name}) بنجاح ✓");
    }

    /**
     * Update the AI Bot configuration for a specific client workspace.
     */
    public function updateBot(Request $request, $id)
    {
        $workspace = Workspace::findOrFail($id);
        $bot = Bot::firstOrCreate(['workspace_id' => $id]);

        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'ai_provider'       => 'required|string|in:gemini,openai,anthropic,openai_compatible',
            'model_type'        => 'required|string|max:100',
            'api_key'           => 'nullable|string|max:500',
            'api_base_url'      => 'nullable|url',
            'temperature'       => 'required|numeric|min:0|max:1',
            'max_tokens'        => 'required|integer|min:50|max:4000',
            'bot_tone'          => 'required|string|in:friendly,formal,sales',
            'system_prompt'     => 'required|string|max:3000',
            'is_active'         => 'nullable|boolean',
            'enable_rag'        => 'nullable|boolean',
            'enable_auto_rules' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['enable_rag'] = $request->has('enable_rag');
        $validated['enable_auto_rules'] = $request->has('enable_auto_rules');

        if ($request->filled('api_key')) {
            $validated['api_key'] = trim($request->api_key);
        } else {
            unset($validated['api_key']);
        }

        $bot->update($validated);

        return back()->with('success', "تم تحديث إعدادات البوت والذكاء الاصطناعي لمتجر ({$workspace->company_name}) بنجاح ✓");
    }

    /**
     * Fetch available models dynamically for admin inspect/edit.
     */
    public function fetchModels(Request $request)
    {
        $provider = $request->input('ai_provider', 'gemini');
        $apiKey = $request->input('ai_api_key');
        $baseUrl = $request->input('api_base_url');

        $bot = Bot::first() ?? new Bot();
        $aiService = new \App\Services\AiService($bot);
        $result = $aiService->fetchAvailableModels($provider, $apiKey, $baseUrl);

        return response()->json($result);
    }

    /**
     * Update workspace plan & subscription price.
     */
    public function updatePlan(Request $request, $id)
    {
        $request->validate([
            'plan_name' => 'required|string',
            'price'     => 'required|numeric|min:0',
        ]);

        $workspace = Workspace::findOrFail($id);
        $workspace->update(['plan_id' => $request->plan_name]);

        Subscription::updateOrCreate(
            ['workspace_id' => $id],
            [
                'plan_name' => $request->plan_name,
                'price'     => $request->price,
                'status'    => 'active',
                'renews_at' => now()->addMonth(),
            ]
        );

        return back()->with('success', 'تم تحديث خطة الاشتراك بنجاح.');
    }

    /**
     * Impersonate a store owner with 1-click login and session tracker.
     */
    public function impersonate($id)
    {
        $workspace = Workspace::findOrFail($id);
        $owner = User::where('workspace_id', $id)->first();

        if (!$owner) {
            return back()->with('error', 'لا يوجد مستخدم مسجل في هذا المتجر لتسجيل الدخول به.');
        }

        $adminId = auth()->id();
        session(['impersonated_by_admin' => $adminId]);
        auth()->login($owner);

        return redirect('/dashboard')->with('info', "أنت تتصفح حالياً بصفتك: {$owner->name} ({$workspace->company_name})");
    }

    /**
     * Leave impersonation session and return to Super Admin.
     */
    public function leaveImpersonation()
    {
        $adminId = session('impersonated_by_admin');

        if ($adminId) {
            $adminUser = User::find($adminId);
            if ($adminUser) {
                session()->forget('impersonated_by_admin');
                auth()->login($adminUser);
                return redirect()->route('admin.workspaces.index')->with('success', 'تمت العودة بنجاح إلى لوحة الإدارة العليا (Super Admin) ✓');
            }
        }

        return redirect('/dashboard');
    }

    /**
     * Instantly switch the Super Admin active workspace context.
     */
    public function switchWorkspace(Request $request)
    {
        $request->validate([
            'workspace_id' => 'required|exists:workspaces,id',
        ]);

        $user = auth()->user();
        if ($user && $user->isSuperAdmin()) {
            $workspace = Workspace::findOrFail($request->workspace_id);
            $user->update(['workspace_id' => $workspace->id]);
            return back()->with('status', "تم تحويل مساحة العمل النشطة فورياً إلى ({$workspace->company_name}) 🏢");
        }

        return back()->with('error', 'غير مصرح بهذا الإجراء.');
    }

    /**
     * Delete a workspace and its associated data safely.
     */
    public function destroy($id)
    {
        if ($id == 1) {
            return back()->with('error', 'لا يمكن حذف مساحة العمل الرئيسية للإدارة العليا.');
        }

        $workspace = Workspace::findOrFail($id);
        $companyName = $workspace->company_name;

        DB::transaction(function () use ($workspace) {
            // Re-assign any super admins currently assigned to this workspace back to Workspace 1
            User::where('workspace_id', $workspace->id)
                ->where('role', 'super_admin')
                ->update(['workspace_id' => 1]);

            $workspace->users()->where('role', '!=', 'super_admin')->delete();
            $workspace->bots()->delete();
            $workspace->conversations()->delete();
            $workspace->customers()->delete();
            $workspace->delete();
        });

        return redirect()->route('admin.workspaces.index')->with('success', "تم حذف المتجر والمساحة ({$companyName}) وجميع بياناتها بنجاح.");
    }
}
