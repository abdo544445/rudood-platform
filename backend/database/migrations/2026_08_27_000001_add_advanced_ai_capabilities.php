<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for Advanced High-Impact AI Capabilities.
     */
    public function up(): void
    {
        // 1. Add context_summary to conversations table
        Schema::table('conversations', function (Blueprint $table) {
            if (!Schema::hasColumn('conversations', 'context_summary')) {
                $table->text('context_summary')->nullable()->after('escalation_reason');
            }
        });

        // 2. Add media_type and media_url to messages table for voice notes & attachments
        Schema::table('messages', function (Blueprint $table) {
            if (!Schema::hasColumn('messages', 'media_type')) {
                $table->string('media_type', 30)->default('text')->after('content');
            }
            if (!Schema::hasColumn('messages', 'media_url')) {
                $table->string('media_url', 500)->nullable()->after('media_type');
            }
        });

        // 3. Add chunks_embeddings to knowledge_bases table
        Schema::table('knowledge_bases', function (Blueprint $table) {
            if (!Schema::hasColumn('knowledge_bases', 'chunks_embeddings')) {
                $table->longText('chunks_embeddings')->nullable()->after('chunks_json');
            }
        });

        // 4. Create mock_orders table to power live AI Function Calling & Tools
        if (!Schema::hasTable('mock_orders')) {
            Schema::create('mock_orders', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('workspace_id')->nullable()->index();
                $table->string('order_number')->unique()->index();
                $table->string('customer_name')->nullable();
                $table->string('customer_phone')->nullable()->index();
                $table->string('status')->default('shipped'); // preparing, shipped, delivered, cancelled
                $table->string('courier')->nullable(); // Aramex, SMSA, DHL, RedBox
                $table->string('tracking_number')->nullable();
                $table->text('items_summary')->nullable();
                $table->decimal('total_amount', 10, 2)->default(0.00);
                $table->string('estimated_delivery')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            if (Schema::hasColumn('conversations', 'context_summary')) {
                $table->dropColumn('context_summary');
            }
        });

        Schema::table('messages', function (Blueprint $table) {
            if (Schema::hasColumn('messages', 'media_type')) {
                $table->dropColumn(['media_type', 'media_url']);
            }
        });

        Schema::table('knowledge_bases', function (Blueprint $table) {
            if (Schema::hasColumn('knowledge_bases', 'chunks_embeddings')) {
                $table->dropColumn('chunks_embeddings');
            }
        });

        Schema::dropIfExists('mock_orders');
    }
};
