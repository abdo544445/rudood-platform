<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Workspace;
use App\Models\Bot;
use App\Models\User;
use App\Models\Subscription;

class SubscriberRequest extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'company_name',
        'selected_plan',
        'notes',
        'status',
        'admin_notes',
        'approved_by',
        'approved_at',
        'created_user_id',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    /**
     * Scope for pending requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Approver user relation.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Created user relation.
     */
    public function createdUser()
    {
        return $this->belongsTo(User::class, 'created_user_id');
    }

    /**
     * Approve and automatically provision the Workspace, Bot, User, and Subscription.
     *
     * @param array $customParams
     * @param int|null $adminId
     * @return User
     */
    public function approveAndProvision(array $customParams = [], ?int $adminId = null): User
    {
        return DB::transaction(function () use ($customParams, $adminId) {
            $companyName = $customParams['company_name'] ?? $this->company_name ?? ($this->name . "'s Store");
            $plan = $customParams['selected_plan'] ?? $this->selected_plan ?? 'professional';
            $password = $customParams['password'] ?? 'password123';
            $botName = $customParams['bot_name'] ?? ('مساعد ' . $companyName . ' الذكي');
            $aiProvider = $customParams['ai_provider'] ?? 'gemini';
            $modelType = $customParams['model_type'] ?? 'gemini-1.5-flash';
            $botTone = $customParams['bot_tone'] ?? 'friendly';
            $systemPrompt = $customParams['system_prompt'] ?? 'أنت مساعد خدمة عملاء ذكي وخبير لمتجر ' . $companyName . '، تجيب على استفسارات الأسعار والمنتجات والشحن بلباقة وسرعة.';
            $welcomeMessage = $customParams['welcome_message'] ?? 'أهلاً بك! 👋 مرحباً بكم في ' . $companyName . '، كيف يمكنني مساعدتك اليوم؟';

            // 1. Create or Find Workspace
            $workspace = Workspace::create([
                'company_name' => $companyName,
                'status'       => 'active',
                'plan_id'      => $plan,
            ]);

            // 2. Create Bot
            $bot = Bot::create([
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
                    'starter'      => 39.00,
                    'enterprise'   => 199.00,
                    default        => 79.00,
                },
                'status'       => 'active',
                'renews_at'    => now()->addMonth(),
            ]);

            // 4. Create or Update Owner User
            $user = User::where('email', $this->email)->first();
            if (!$user) {
                $user = User::create([
                    'name'         => $this->name,
                    'email'        => $this->email,
                    'phone'        => $this->phone,
                    'password'     => Hash::make($password),
                    'workspace_id' => $workspace->id,
                    'role'         => 'owner',
                ]);
            } else {
                $user->update([
                    'workspace_id' => $workspace->id,
                    'role'         => 'owner',
                ]);
            }

            // 5. Update Request Status
            $this->update([
                'status'          => 'approved',
                'approved_by'     => $adminId ?? (auth()->id() ?? null),
                'approved_at'     => now(),
                'created_user_id' => $user->id,
            ]);

            return $user;
        });
    }

    /**
     * Get the formatted welcome message text.
     */
    public static function getWelcomeNotificationText(string $subscriberName = '', string $companyName = ''): string
    {
        return "تمت إضافتك بنجاح! أهلاً بكم كشريك ومستخدم في منصة ردود. تفضل بالدخول لصفحة متجرك وزود البوت ببيانات وآلية عمل متجرك 🚀";
    }
}
