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
        Schema::create('channels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->onDelete('cascade');
            $table->string('platform'); // whatsapp, telegram, instagram, web
            $table->string('label')->nullable();
            // WhatsApp Cloud API
            $table->text('access_token')->nullable();       // encrypted
            $table->string('phone_number_id')->nullable();
            $table->text('verify_token')->nullable();       // encrypted
            // Telegram Bot
            $table->text('bot_token')->nullable();          // encrypted
            $table->string('bot_username')->nullable();
            $table->string('chat_id')->nullable();
            $table->string('webhook_url')->nullable();
            // State
            $table->boolean('is_connected')->default(false);
            $table->text('last_error')->nullable();
            $table->timestamp('connected_at')->nullable();
            $table->timestamps();

            // Unique channel per (workspace, platform)
            $table->unique(['workspace_id', 'platform']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('channels');
    }
};
