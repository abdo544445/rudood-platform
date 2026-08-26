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
        Schema::table('messages', function (Blueprint $table) {
            $table->index(['conversation_id', 'created_at']);
            $table->index(['sender_type', 'created_at']);
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->index(['workspace_id', 'status', 'updated_at']);
            $table->index(['workspace_id', 'customer_id', 'status']);
        });

        Schema::table('auto_rules', function (Blueprint $table) {
            $table->index(['workspace_id', 'is_active']);
        });

        Schema::table('knowledge_bases', function (Blueprint $table) {
            $table->index(['bot_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('knowledge_bases', function (Blueprint $table) {
            $table->dropIndex(['bot_id', 'status']);
        });

        Schema::table('auto_rules', function (Blueprint $table) {
            $table->dropIndex(['workspace_id', 'is_active']);
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->dropIndex(['workspace_id', 'status', 'updated_at']);
            $table->dropIndex(['workspace_id', 'customer_id', 'status']);
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex(['conversation_id', 'created_at']);
            $table->dropIndex(['sender_type', 'created_at']);
        });
    }
};
