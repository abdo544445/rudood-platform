<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            if (!Schema::hasColumn('conversations', 'is_bot_paused')) {
                $table->boolean('is_bot_paused')->default(false)->after('status');
            }
            if (!Schema::hasColumn('conversations', 'bot_paused_until')) {
                $table->timestamp('bot_paused_until')->nullable()->after('is_bot_paused');
            }
            if (!Schema::hasColumn('conversations', 'sentiment')) {
                $table->string('sentiment')->default('neutral')->after('bot_paused_until'); // positive, neutral, negative, urgent
            }
            if (!Schema::hasColumn('conversations', 'is_escalated')) {
                $table->boolean('is_escalated')->default(false)->after('sentiment');
            }
            if (!Schema::hasColumn('conversations', 'escalation_reason')) {
                $table->string('escalation_reason')->nullable()->after('is_escalated');
            }
            if (!Schema::hasColumn('conversations', 'notes')) {
                $table->text('notes')->nullable()->after('escalation_reason');
            }
            if (!Schema::hasColumn('conversations', 'tags')) {
                $table->json('tags')->nullable()->after('notes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropColumn([
                'is_bot_paused',
                'bot_paused_until',
                'sentiment',
                'is_escalated',
                'escalation_reason',
                'notes',
                'tags',
            ]);
        });
    }
};
