<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('workspace_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('action');       // e.g. bot_updated, user_reset_password, byok_toggled, live_chat_takeover
                $table->string('category')->default('system'); // bot, user, security, chat, billing
                $table->text('description');
                $table->string('ip_address')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['workspace_id', 'created_at']);
                $table->index(['action']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
