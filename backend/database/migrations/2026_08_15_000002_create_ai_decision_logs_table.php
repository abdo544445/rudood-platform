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
        Schema::create('ai_decision_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('message_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('trigger', ['auto_rule', 'ai_api', 'fallback'])->default('ai_api');
            $table->text('matched_keywords')->nullable();
            $table->longText('context_sent')->nullable();
            $table->string('ai_provider')->nullable();
            $table->string('model_type')->nullable();
            $table->text('customer_message');
            $table->text('bot_reply');
            $table->integer('response_time_ms')->default(0);
            $table->timestamps();

            $table->index(['conversation_id', 'created_at']);
            $table->index('trigger');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_decision_logs');
    }
};
