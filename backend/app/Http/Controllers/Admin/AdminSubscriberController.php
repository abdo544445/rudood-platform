<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\SubscriberRequest;
use App\Models\Workspace;
use App\Models\User;
use App\Models\Bot;
use App\Models\Subscription;
use App\Models\AuditLog;

class AdminSubscriberController extends Controller
{
    /**
     * Display a listing of subscriber requests and active subscribers.
     */
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');
        $search = $request->query('search');

        $query = SubscriberRequest::with(['approver', 'createdUser'])
            ->latest();

        if ($status !== 'all' && in_array($status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        $requests = $query->paginate(15)->withQueryString();

        $stats = [
            'total'    => SubscriberRequest::count(),
            'pending'  => SubscriberRequest::where('status', 'pending')->count(),
            'approved' => SubscriberRequest::where('status', 'approved')->count(),
            'rejected' => SubscriberRequest::where('status', 'rejected')->count(),
        ];

        return view('admin.subscribers.index', compact('requests', 'stats', 'status', 'search'));
    }

    /**
     * Show the form for creating a new subscriber manually (Client Requirement #5).
     */
    public function create()
    {
        return view('admin.subscribers.create');
    }

    /**
     * Store a newly created subscriber and provision their full workspace & bot.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Subscriber info
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:users,email',
            'phone'           => 'required|string|max:30',
            'password'        => 'required|string|min:6',
            
            // Store & Plan info
            'company_name'    => 'required|string|max:255',
            'selected_plan'   => 'required|string|in:starter,professional,enterprise',
            
            // Bot configs
            'bot_name'        => 'nullable|string|max:255',
            'ai_provider'     => 'required|string|in:gemini,openai,claude',
            'model_type'      => 'nullable|string|max:100',
            'bot_tone'        => 'required|string|in:friendly,formal,enthusiastic,empathetic',
            'system_prompt'   => 'nullable|string|max:2000',
            'welcome_message' => 'nullable|string|max:1000',
            'admin_notes'     => 'nullable|string|max:1000',
        ]);

        $user = DB::transaction(function () use ($validated, $request) {
            $companyName = $validated['company_name'];
            $plan = $validated['selected_plan'];
            $botName = ($validated['bot_name'] ?? null) ?: ('مساعد ' . $companyName . ' الذكي');
            $aiProvider = $validated['ai_provider'];
            $modelType = ($validated['model_type'] ?? null) ?: match($aiProvider) {
                'openai' => 'gpt-4o-mini',
                'claude' => 'claude-3-5-sonnet',
                default  => 'gemini-1.5-flash',
            };
            $botTone = $validated['bot_tone'];
            $systemPrompt = ($validated['system_prompt'] ?? null) ?: ('أنت مساعد خدمة عملاء ذكي ومحترف لمتجر ' . $companyName . '، تجيب على استفسارات العملاء بدقة وسرعة.');
            $welcomeMessage = ($validated['welcome_message'] ?? null) ?: ('أهلاً بك! 👋 مرحباً بكم في ' . $companyName . '، كيف يمكنني خدمتك اليوم؟');

            // 1. Create Workspace
            $workspace = Workspace::create([
                'company_name' => $companyName,
                'status'       => 'active',
                'plan_id'      => $plan,
            ]);

            // 2. Create Bot
            Bot::create([
                'workspace_id'    => $workspace->id,
                'name'            => $botName,
                'system_prompt'   => $systemPrompt,
                'welcome_message' => $welcomeMessage,
                'bot_tone'        => $botTone,
                'ai_provider'     => $aiProvider,
                'model_type'      => $modelType,
                'temperature'     => 0.7,
                'max_tokens'      => 600,
                'is_active'       => true,
            ]);

            // 3. Create Subscription
            Subscription::create([
                'workspace_id' => $workspace->id,
                'plan_name'    => $plan,
                'price'        => match($plan) {
                    'starter'    => 39.00,
                    'enterprise' => 199.00,
                    default      => 79.00,
                },
                'status'       => 'active',
                'renews_at'    => now()->addMonth(),
            ]);

            // 4. Create User
            $user = User::create([
                'name'         => $validated['name'],
                'email'        => $validated['email'],
                'phone'        => $validated['phone'],
                'password'     => Hash::make($validated['password']),
                'workspace_id' => $workspace->id,
                'role'         => 'owner',
            ]);

            // 5. Create Approved Subscriber Request record for audit trail
            SubscriberRequest::create([
                'name'            => $validated['name'],
                'email'           => $validated['email'],
                'phone'           => $validated['phone'],
                'company_name'    => $companyName,
                'selected_plan'   => $plan,
                'admin_notes'     => $validated['admin_notes'] ?? 'تمت إضافة المشترك يدوياً وتفعيله بالكامل بواسطة المشرف العام.',
                'status'          => 'approved',
                'approved_by'     => auth()->id(),
                'approved_at'     => now(),
                'created_user_id' => $user->id,
            ]);

            // Audit Log
            AuditLog::create([
                'user_id'      => auth()->id(),
                'workspace_id' => $workspace->id,
                'action'       => 'subscriber.created_manually',
                'description'  => "قام المشرف بإضافة مشترك جديد '{$user->name}' للمتجر '{$companyName}' بريد '{$user->email}'.",
                'ip_address'   => $request->ip(),
            ]);

            return $user;
        });

        $welcomeNotice = SubscriberRequest::getWelcomeNotificationText($user->name, $validated['company_name']);

        return redirect()->route('admin.subscribers.index')->with([
            'success'        => "تمت إضافة وتفعيل المشترك '{$user->name}' ومساحة عمل متجره بنجاح! 🚀",
            'welcome_notice' => $welcomeNotice,
        ]);
    }

    /**
     * Approve a pending subscriber request and provision their workspace & bot (Client Requirement #2).
     */
    public function approve(Request $request, $id)
    {
        $subRequest = SubscriberRequest::findOrFail($id);

        if ($subRequest->status === 'approved') {
            return back()->with('info', 'هذا الطلب معتمد ومفعل مسبقاً.');
        }

        $user = $subRequest->approveAndProvision([
            'admin_notes' => $request->input('admin_notes'),
        ], auth()->id());

        AuditLog::create([
            'user_id'      => auth()->id(),
            'workspace_id' => $user->workspace_id,
            'action'       => 'subscriber.approved',
            'description'  => "تمت الموافقة على طلب المشترك '{$subRequest->name}' ({$subRequest->email}) وتفعيل مساحة العمل.",
            'ip_address'   => $request->ip(),
        ]);

        $welcomeNotice = SubscriberRequest::getWelcomeNotificationText($subRequest->name, $subRequest->company_name ?? '');

        return back()->with([
            'success'        => "تم اعتماد البريد الإلكتروني للمشترك وتفعيل حسابه وبوت متجره بنجاح! 🎉",
            'welcome_notice' => $welcomeNotice,
        ]);
    }

    /**
     * Reject or close a subscriber request.
     */
    public function reject(Request $request, $id)
    {
        $subRequest = SubscriberRequest::findOrFail($id);
        $subRequest->update([
            'status'      => 'rejected',
            'admin_notes' => $request->input('admin_notes', 'تم الرفض أو عدم الاتفاق.'),
            'approved_by' => auth()->id(),
        ]);

        AuditLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'subscriber.rejected',
            'description' => "تم رفض طلب المشترك '{$subRequest->name}' ({$subRequest->email}).",
            'ip_address'  => $request->ip(),
        ]);

        return back()->with('info', "تم تحديث حالة الطلب إلى (مرفوض / غير متفق).");
    }

    /**
     * Delete a subscriber request.
     */
    public function destroy($id)
    {
        $subRequest = SubscriberRequest::findOrFail($id);
        $subRequest->delete();

        return back()->with('success', 'تم حذف سجل الطلب بنجاح.');
    }
}
