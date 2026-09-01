<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations for Knowledge Chunks & PostgreSQL Vector Storage.
     */
    public function up(): void
    {
        // 1. Enable pgvector extension if running on PostgreSQL
        try {
            if (DB::getDriverName() === 'pgsql') {
                DB::statement('CREATE EXTENSION IF NOT EXISTS vector;');
            }
        } catch (\Throwable $e) {
            \Log::info('pgvector extension check: ' . $e->getMessage());
        }

        // 2. Create knowledge_chunks table
        if (!Schema::hasTable('knowledge_chunks')) {
            Schema::create('knowledge_chunks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('knowledge_base_id')->constrained('knowledge_bases')->onDelete('cascade');
                $table->unsignedBigInteger('bot_id')->index();
                $table->unsignedBigInteger('workspace_id')->nullable()->index();
                $table->unsignedInteger('chunk_index')->default(0);
                $table->longText('chunk_text');
                $table->longText('embedding')->nullable(); // JSON float array for universal compatibility
                $table->unsignedInteger('token_count')->default(0);
                $table->json('metadata')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knowledge_chunks');
    }
};
