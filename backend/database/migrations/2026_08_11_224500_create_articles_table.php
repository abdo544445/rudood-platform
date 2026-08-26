<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->text('summary')->nullable();
            $table->longText('content');
            $table->string('category')->default('أتمتة وذكاء اصطناعي');
            $table->string('read_time')->default('قراءة 5 دقائق');
            $table->string('icon')->default('bi-robot');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        // Seed initial articles
        DB::table('articles')->insert([
            [
                'slug'         => 'how-ai-boosts-sales-40-percent',
                'title'        => 'كيف يرفع الذكاء الاصطناعي التكيفي مبيعات متجرك بنسبة 40%؟',
                'summary'      => 'التعرف على كيفية استخدام تقنيات الشات بوت الحديثة للرد اللحظي على استفسارات العملاء ومتابعة السلات المتروكة تلقائياً دون تدخل بشري.',
                'content'      => '<p class="lead text-white fw-bold mb-4">في عالم التجارة الإلكترونية السريع، سرعة الرد على العملاء ليست مجرد ميزة رفاهية، بل هي الفارق الحقيقي بين إتمام عملية البيع أو خروج العميل وتوجهه للمنافسين.</p><h3 class="text-gold fw-bold mt-5 mb-3">1. الاستجابة اللحظية في جميع الأوقات</h3><p>تشير الدراسات إلى أن العميل يتوقع الرد خلال أقل من 5 دقائق عند الاستفسار عبر المحادثات المباشرة أو واتساب. باستخدام شات بوت منصة <strong>ردود</strong> المتطورة، يتم الرد على استفسارات الأسعار، التوصيل، وحالة الطلبات فوراً في أي وقت على مدار 24 ساعة.</p><h3 class="text-gold fw-bold mt-5 mb-3">2. استعادة السلات المتروكة تلقائياً</h3><p>يترك معظم المتسوقين أسرّتهم ومشترياتهم في السلة قبل الخطوة الأخيرة. يمكن لربط المتجر بالذكاء الاصطناعي إرسال تذكير مخصص ولطيف للعميل عبر الواتساب مع رابط مباشر لإتمام الشراء، مما يعيد نسبة كبيرة من هذه المبيعات الضائعة.</p><div class="p-4 my-4 rounded-3 border-start border-gold border-4 bg-gold-subtle text-white"><i class="bi bi-quote text-gold fs-3 d-block mb-2"></i><p class="mb-0 fst-italic">"العميل لا ينتظر.. السرعة والموثوقية في الرد الآلي هي المفتاح الأول لتحويل الاستفسار العابر إلى صفقة ناجحة."</p></div><h3 class="text-gold fw-bold mt-5 mb-3">3. تحليل سلوك العميل وتخصيص العروض</h3><p>لا يقتصر دور ردود على الإجابة الجافة، بل يتعلم نظام الذكاء الاصطناعي من تفضيلات العملاء، ويقترح عليهم المنتجات المناسبة بناءً على اهتماماتهم السابقة، مما يرفع متوسط قيمة السلة الشرائية بشكل ملحوظ.</p>',
                'category'     => 'أتمتة وذكاء اصطناعي',
                'read_time'    => 'قراءة 5 دقائق',
                'icon'         => 'bi-robot',
                'is_featured'  => true,
                'is_published' => true,
                'published_at' => now(),
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'slug'         => 'whatsapp-business-integration-guide',
                'title'        => 'دليل ربط واتساب الأعمال بمنصة ردود خلال 5 دقائق',
                'summary'      => 'شرح خطوة بخطوة لربط رقمك التجاري والاستفادة من الرد الفوري الموحد لجميع عملائك.',
                'content'      => '<p class="lead text-white fw-bold mb-4">يعتبر تطبيق واتساب الأعمال الوسيلة الأولى للتواصل والتسويق المباشر في منطقة الشرق الأوسط.</p><p>باستخدام واجهات برمجة التطبيقات الرسمية من Meta ومنصة ردود، يمكنك ربط رقمك وتفعيل الردود التلقائية الذكية، وتعيين المحادثات لأفراد الفريق بأسلوب منظم وسلس.</p>',
                'category'     => 'أتمتة المحادثات',
                'read_time'    => 'قراءة 4 دقائق',
                'icon'         => 'bi-whatsapp',
                'is_featured'  => false,
                'is_published' => true,
                'published_at' => now(),
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'slug'         => '5-fatal-customer-service-mistakes',
                'title'        => '5 أخطاء قاتلة في خدمة العملاء تكلفك فقدان المبيعات',
                'summary'      => 'التأخر في الرد والإجابات الإنشائية قد تبعد عنك العميل. تعرف على كيفية حل هذه المشاكل بذكاء.',
                'content'      => '<p class="lead text-white fw-bold mb-4">خدمة العملاء السيئة كفيلة بإغلاق متجرك وإبعاد المبيعات حتى مع وجود ميزانيات تسويق ضخمة.</p><p>أبرز هذه الأخطاء هو البطء الشديد في الرد وعدم توفير إجابات واضحة، بالإضافة إلى غياب الدعم خارج ساعات العمل الرسمية.</p>',
                'category'     => 'خدمة العملاء',
                'read_time'    => 'قراءة 6 دقائق',
                'icon'         => 'bi-headset',
                'is_featured'  => false,
                'is_published' => true,
                'published_at' => now(),
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'slug'         => 'understand-customer-behavior-analytics',
                'title'        => 'كيف تفهم سلوك عملائك من خلال لوحة تحليلات ردود؟',
                'summary'      => 'قراءة البيانات وتحليل أكثر الأسئلة تكراراً تساعدك في تطوير منتجاتك وتسريع عمليات البيع.',
                'content'      => '<p class="lead text-white fw-bold mb-4">تعتبر البيانات هي الأصول الأكثر قيمة لأي رائد أعمال في العصر الرقمي.</p><p>تقدم لك لوحة تحليلات منصة ردود تقارير فورية عن أكثر الكلمات تكراراً، وأوقات الذروة التي يزداد فيها تواصل العملاء.</p>',
                'category'     => 'تحليلات وأرقام',
                'read_time'    => 'قراءة 5 دقائق',
                'icon'         => 'bi-graph-up-arrow',
                'is_featured'  => false,
                'is_published' => true,
                'published_at' => now(),
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
