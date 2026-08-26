<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Workspaces: Add allow_custom_api_key
        Schema::table('workspaces', function (Blueprint $table) {
            if (!Schema::hasColumn('workspaces', 'allow_custom_api_key')) {
                $table->boolean('allow_custom_api_key')->default(false)->after('plan_id');
            }
        });

        // 2. Bots: Add enable_rag, enable_auto_rules, api_mode
        Schema::table('bots', function (Blueprint $table) {
            if (!Schema::hasColumn('bots', 'enable_rag')) {
                $table->boolean('enable_rag')->default(true)->after('is_active');
            }
            if (!Schema::hasColumn('bots', 'enable_auto_rules')) {
                $table->boolean('enable_auto_rules')->default(true)->after('enable_rag');
            }
            if (!Schema::hasColumn('bots', 'api_mode')) {
                $table->string('api_mode')->default('platform_central')->after('ai_provider');
            }
        });

        // 3. KnowledgeBase: Add chunks_json
        Schema::table('knowledge_bases', function (Blueprint $table) {
            if (!Schema::hasColumn('knowledge_bases', 'chunks_json')) {
                $table->json('chunks_json')->nullable()->after('document_text');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workspaces', function (Blueprint $table) {
            if (Schema::hasColumn('workspaces', 'allow_custom_api_key')) {
                $table->dropColumn('allow_custom_api_key');
            }
        });

        Schema::table('bots', function (Blueprint $table) {
            $table->dropColumn(['enable_rag', 'enable_auto_rules', 'api_mode']);
        });

        Schema::table('knowledge_bases', function (Blueprint $table) {
            if (Schema::hasColumn('knowledge_bases', 'chunks_json')) {
                $table->dropColumn('chunks_json');
            }
        });
    }
};
