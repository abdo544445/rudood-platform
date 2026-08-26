<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Workspace;
use App\Models\User;
use App\Models\Bot;
use App\Models\AutoRule;
use App\Models\KnowledgeBase;
use App\Models\Customer;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Subscription;
use App\Models\Article;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with complete test datasets.
     */
    public function run(): void
    {
        // ── 1. Create Super Admin Workspace & User ──────────────────────────────
        $adminWorkspace = Workspace::firstOrCreate(
            ['company_name' => 'إدارة منصة ردود (Rudood Admin HQ)'],
            ['plan_id' => 'enterprise', 'status' => 'active']
        );

        $adminUser = User::firstOrCreate(
            ['email' => 'admin@rudood.com'],
            [
                'name'         => 'مدير النظام الأعلى',
                'phone'        => '+966500000000',
                'password'     => Hash::make('password123'),
                'role'         => 'super_admin',
                'workspace_id' => $adminWorkspace->id,
            ]
        );

        // ── 2. Create Demo Workspace: متجر النخبة للتجارة ─────────────────────────
        $storeWorkspace = Workspace::firstOrCreate(
            ['company_name' => 'متجر النخبة للتجارة الإلكترونية'],
            ['plan_id' => 'professional', 'status' => 'active']
        );

        $storeOwner = User::firstOrCreate(
            ['email' => 'owner@store.com'],
            [
                'name'         => 'عبدالله التميمي',
                'phone'        => '+966551234567',
                'password'     => Hash::make('password123'),
                'role'         => 'owner',
                'workspace_id' => $storeWorkspace->id,
            ]
        );

        Subscription::firstOrCreate(
            ['workspace_id' => $storeWorkspace->id],
            [
                'plan_name' => 'professional',
                'price'     => 79.00,
                'status'    => 'active',
                'renews_at' => now()->addMonth(),
            ]
        );

        // Create Bot for Store Workspace
        $storeBot = Bot::firstOrCreate(
            ['workspace_id' => $storeWorkspace->id],
            [
                'name'            => 'مساعد متجر النخبة الذكي',
                'system_prompt'   => 'أنت مساعد خدمة عملاء ذكي وخبير لمتجر النخبة، تجيب على استفسارات الأسعار والشحن وطرق الدفع بلباقة واحترافية باللغة العربية.',
                'welcome_message' => 'أهلاً بك في متجر النخبة! 🛍️ كيف يمكنني مساعدتك اليوم بخصوص طلباتك أو منتجاتنا؟',
                'bot_tone'        => 'friendly',
                'ai_provider'     => 'gemini',
                'model_type'      => 'gemini-1.5-flash',
                'temperature'     => 0.7,
                'max_tokens'      => 600,
                'is_active'       => true,
            ]
        );

        // Create Auto Rules for Store Bot
        AutoRule::firstOrCreate(
            ['workspace_id' => $storeWorkspace->id, 'question' => 'ما هي أوقات العمل والتوصيل؟'],
            [
                'keywords'         => ['اوقات', 'أوقات', 'ساعات', 'العمل', 'التوصيل', 'دوام'],
                'trigger_condition'=> 'contains',
                'reply_template'   => "أوقات العمل واستقبال الطلبات لدينا على مدار الساعة 24/7! ⏰\nمدة التوصيل داخل الرياض خلال 24 ساعة، وباقي مدن المملكة من 2 إلى 4 أيام عمل.",
                'is_active'        => true,
            ]
        );

        AutoRule::firstOrCreate(
            ['workspace_id' => $storeWorkspace->id, 'question' => 'ما هي طرق الدفع المتاحة؟'],
            [
                'keywords'         => ['الدفع', 'دفع', 'مدى', 'فيزا', 'تقسيط', 'تمارا', 'تابي', 'stc'],
                'trigger_condition'=> 'contains',
                'reply_template'   => "نوفر جميع وسائل الدفع الآمنة: مدى، فيزا، ماستركارد، Apple Pay، بالإضافة لخدمات التقسيط المريح عبر (تابي) و(تمارا) بدون فوائد! 💳",
                'is_active'        => true,
            ]
        );

        AutoRule::firstOrCreate(
            ['workspace_id' => $storeWorkspace->id, 'question' => 'ما هي سياسة الاسترجاع والاستبدال؟'],
            [
                'keywords'         => ['استرجاع', 'استبدال', 'ارجاع', 'ترجيع', 'ضمان'],
                'trigger_condition'=> 'contains',
                'reply_template'   => "يمكنك استرجاع أو استبدال أي منتج خلال 7 أيام من تاريخ الاستلام بشرط بقاء المنتج في حالته الأصلية وبغلافه. الاسترجاع مجاني في حال وجود عيب مصنعي.",
                'is_active'        => true,
            ]
        );

        // Knowledge Base Document for Store Bot
        KnowledgeBase::firstOrCreate(
            ['bot_id' => $storeBot->id, 'file_name' => 'دليل_المنتجات_والأسعار_والسياسات.txt'],
            [
                'file_path'     => 'knowledge/elite_store_manual.txt',
                'document_text' => "متجر النخبة متخصص في الأجهزة الإلكترونية الذكية والإكسسوارات الفاخرة.\nالمنتج الأكثر مبيعاً: سماعات النخبة اللاسلكية بسعر 199 ريال شامل الضريبة وضمان سنتين.\nساعة النخبة الرياضية: مقاومة للماء مع شاشة AMOLED بسعر 299 ريال.\nالشحن مجاني لجميع الطلبات التي تتجاوز قيمتها 300 ريال سعودي.",
            ]
        );

        // Demo Customers & Conversations
        $customer1 = Customer::firstOrCreate(
            ['workspace_id' => $storeWorkspace->id, 'phone' => '+966540000001'],
            [
                'name'     => 'سارة الشمري',
                'email'    => 'sara@example.com',
                'platform' => 'whatsapp',
            ]
        );

        $conv1 = Conversation::firstOrCreate(
            ['workspace_id' => $storeWorkspace->id, 'customer_id' => $customer1->id],
            ['status' => 'open']
        );

        Message::firstOrCreate(
            ['conversation_id' => $conv1->id, 'content' => 'مرحباً، هل يتوفر لديكم تقسيط عبر تابي؟'],
            ['sender_type' => 'customer', 'created_at' => now()->subMinutes(30)]
        );

        Message::firstOrCreate(
            ['conversation_id' => $conv1->id, 'content' => "نوفر جميع وسائل الدفع الآمنة: مدى، فيزا، ماستركارد، Apple Pay، بالإضافة لخدمات التقسيط المريح عبر (تابي) و(تمارا) بدون فوائد! 💳"],
            ['sender_type' => 'bot', 'created_at' => now()->subMinutes(29)]
        );

        $customer2 = Customer::firstOrCreate(
            ['workspace_id' => $storeWorkspace->id, 'phone' => '+966560000002'],
            [
                'name'     => 'خالد المطيري',
                'email'    => 'khaled@example.com',
                'platform' => 'web',
            ]
        );

        $conv2 = Conversation::firstOrCreate(
            ['workspace_id' => $storeWorkspace->id, 'customer_id' => $customer2->id],
            ['status' => 'open']
        );

        Message::firstOrCreate(
            ['conversation_id' => $conv2->id, 'content' => 'السلام عليكم، كم سعر سماعات النخبة اللاسلكية؟'],
            ['sender_type' => 'customer', 'created_at' => now()->subMinutes(10)]
        );

        Message::firstOrCreate(
            ['conversation_id' => $conv2->id, 'content' => 'وعليكم السلام ورحمة الله! سعر سماعات النخبة اللاسلكية هو 199 ريال شامل الضريبة مع ضمان استبدال لمدة سنتين.'],
            ['sender_type' => 'bot', 'created_at' => now()->subMinutes(9)]
        );

        // Seed Channels for Store Workspace
        \App\Models\Channel::firstOrCreate(
            ['workspace_id' => $storeWorkspace->id, 'platform' => 'whatsapp'],
            [
                'label'           => 'حساب واتساب للأعمال',
                'access_token'    => 'demo_wa_access_token_123',
                'phone_number_id' => '109988776655',
                'verify_token'    => 'rudood_secret',
                'is_connected'    => true,
                'connected_at'    => now(),
            ]
        );

        \App\Models\Channel::firstOrCreate(
            ['workspace_id' => $storeWorkspace->id, 'platform' => 'telegram'],
            [
                'label'        => 'بوت تيليجرام التجريبي',
                'bot_token'    => '123456789:ABCdefGhIJKlmNoPQRstuVWXyz',
                'bot_username' => 'EliteStoreBot',
                'webhook_url'  => 'https://rudood.com/api/webhook/telegram/' . $storeWorkspace->id,
                'is_connected' => true,
                'connected_at' => now(),
            ]
        );

        // ── 3. Seed Initial Blog Articles ─────────────────────────────────────────
        Article::firstOrCreate(
            ['slug' => 'how-ai-transforms-customer-service-2026'],
            [
                'title'       => 'كيف يغير الذكاء الاصطناعي مستقبل خدمة العملاء في التجارة الإلكترونية؟',
                'summary'     => 'تعرف على كيفية مضاعفة مبيعات متجرك وتقليل تكاليف الدعم الفني بنسبة 70% باستخدام أتمتة الردود الفورية.',
                'content'     => '<h2>ثورة الذكاء الاصطناعي في خدمة العملاء</h2><p>في عالم التجارة الإلكترونية المتسارع، لم يعد العميل ينتظر ساعات لتلقي رد على استفسار حول حالة الشحنة أو تفاصيل المنتج. توفر منصات خدمة العملاء الذكية استجابة فورية 24/7 تفهم اللهجات المحلية وتقدم حلولاً مخصصة لكل عميل.</p><h3>أهم المزايا:</h3><ul><li>استجابة فورية خلال أجزاء من الثانية.</li><li>ربط شامل مع واتساب وقنوات التواصل.</li><li>توفير هائل في تكاليف مراكز الاتصال.</li></ul>',
                'category'    => 'الذكاء الاصطناعي',
                'read_time'   => '4 دقائق',
                'is_published'=> true,
                'is_featured' => true,
                'published_at'=> now(),
            ]
        );

        Article::firstOrCreate(
            ['slug' => 'best-practices-whatsapp-automation'],
            [
                'title'       => 'دليلك الشامل لربط وأتمتة محادثات واتساب للأعمال',
                'summary'     => 'خطوات عملية لربط بوت الرد الآلي بحسابك التجاري على WhatsApp Cloud API لتحقيق أعلى تفاعل مع زوار متجرك.',
                'content'     => '<h2>لماذا يعتبر واتساب القناة الأهم لمتاجرك؟</h2><p>يمتلك تطبيق واتساب أعلى معدل فتح للرسائل يتجاوز 98%. من خلال أتمتة رسائل السلات المتروكة، وتأكيد الطلبات، وتتبع الشحنات عبر الـ WhatsApp Cloud API، يمكنك زيادة التحويلات وتحسين تجربة العميل بشكل ملحوظ.</p>',
                'category'    => 'التجارة الإلكترونية',
                'read_time'   => '6 دقائق',
                'is_published'=> true,
                'is_featured' => false,
                'published_at'=> now()->subDays(2),
            ]
        );

        // ── 7. Seed Sample Contact Us Inquiries ────────────────────────────────
        \App\Models\ContactMessage::firstOrCreate(
            ['email' => 'fahad.qahtani@retail.sa'],
            [
                'name'        => 'فهد القحطاني',
                'subject'     => 'استفسار عن خطة الشركات الكبرى والتكامل مع نظام ERP',
                'message'     => "السلام عليكم، نحن شركة تجزئة نملك 14 فرعاً ومتجر إلكتروني. هل تتيح منصة ردود الربط المباشر مع قواعد بيانات أوراكل ونظام سلة لأتمتة فواتير الطلبات وحساب النقاط؟ أرجو التواصل لمناقشة العرض المالي.",
                'status'      => 'new',
                'ip_address'  => '192.168.1.105',
                'created_at'  => now()->subHours(2),
            ]
        );

        \App\Models\ContactMessage::firstOrCreate(
            ['email' => 'noura.alharbi@boutique.com'],
            [
                'name'        => 'نورة الحربي',
                'subject'     => 'طلب تجربة مجانية لبوت إنستغرام والرد على التعليقات',
                'message'     => "مرحباً بكم، لدينا حساب متجر ملابس على إنستغرام يتلقى أكثر من 200 تعليق يومياً على البوستات بخصوص الأسعار. هل يمكن للبوت الرد تلقائياً على التعليق مع إرسال رسالة خاصة في Direct؟",
                'status'      => 'in_progress',
                'ip_address'  => '192.168.1.110',
                'admin_notes' => 'تم التواصل عبر الواتساب وتحديد موعد عرض تجريبي يوم الغد.',
                'created_at'  => now()->subDays(1),
            ]
        );

        \App\Models\ContactMessage::firstOrCreate(
            ['email' => 'khalid.tamimi@logistics.net'],
            [
                'name'        => 'خالد التميمي',
                'subject'     => 'شكر وتقدير + اقتراح إضافة ميزة تتبع الشحنات',
                'message'     => "نشكر فريق ردود على الخدمة الممتازة. نود اقتراح إضافة تكامل مباشر مع شركات الشحن مثل سمسا وأرامكس لعرض حالة الشحنة فورياً في ودجت الدردشة.",
                'status'      => 'resolved',
                'ip_address'  => '192.168.1.120',
                'admin_notes' => 'تم الرد بالايميل وشكر العميل وإدراج الميزة في خطة التطوير Q4.',
                'created_at'  => now()->subDays(3),
            ]
        );

        // ── 8. Seed Sample Mock Orders for AI Tool Calling ─────────────────────
        \App\Models\MockOrder::firstOrCreate(
            ['order_number' => '10492'],
            [
                'customer_name'      => 'سارة العتيبي',
                'customer_phone'     => '+966551122334',
                'status'             => 'shipped',
                'courier'            => 'أرامكس (Aramex)',
                'tracking_number'    => 'ARX-98234190',
                'items_summary'      => 'سماعات النخبة اللاسلكية + شاحن سريع 3 في 1',
                'total_amount'       => 348.00,
                'estimated_delivery' => 'غداً بين الساعة 2 ظهراً و 6 مساءً',
            ]
        );

        \App\Models\MockOrder::firstOrCreate(
            ['order_number' => '10580'],
            [
                'customer_name'      => 'فهد الشمري',
                'customer_phone'     => '+966504455667',
                'status'             => 'preparing',
                'courier'            => 'سمسا (SMSA Express)',
                'tracking_number'    => 'SMSA-7712034',
                'items_summary'      => 'ساعة النخبة الرياضية الذكية AMOLED',
                'total_amount'       => 299.00,
                'estimated_delivery' => 'خلال يومين عمل',
            ]
        );

        \App\Models\MockOrder::firstOrCreate(
            ['order_number' => '10215'],
            [
                'customer_name'      => 'محمد الدوسري',
                'customer_phone'     => '+966567788990',
                'status'             => 'delivered',
                'courier'            => 'ريد بوكس (RedBox)',
                'tracking_number'    => 'RBX-339182',
                'items_summary'      => 'شاحن لاسلكي سريع 3 في 1',
                'total_amount'       => 149.00,
                'estimated_delivery' => 'تم الاستلام بنجاح من خزانة ريد بوكس',
            ]
        );
    }
}
