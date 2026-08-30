<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Make conversation_id nullable in ai_decision_logs table.
     * Playground tests and other non-conversation AI calls don't have
     * a real conversation context, so this column should accept null.
     */
    public function up(): void
    {
        Schema::table('ai_decision_logs', function (Blueprint $table) {
            // Drop the existing foreign key constraint first
            $table->dropForeign(['conversation_id']);

            // Make the column nullable and re-add the FK with nullOnDelete
            $table->unsignedBigInteger('conversation_id')->nullable()->change();
            $table->foreign('conversation_id')
                  ->references('id')
                  ->on('conversations')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse: restore non-nullable conversation_id with cascadeOnDelete.
     */
    public function down(): void
    {
        Schema::table('ai_decision_logs', function (Blueprint $table) {
            $table->dropForeign(['conversation_id']);

            $table->unsignedBigInteger('conversation_id')->nullable(false)->change();
            $table->foreign('conversation_id')
                  ->references('id')
                  ->on('conversations')
                  ->cascadeOnDelete();
        });
    }
};
