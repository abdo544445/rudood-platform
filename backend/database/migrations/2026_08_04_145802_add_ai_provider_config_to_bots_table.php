<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds multi-provider AI configuration to the bots table.
     * Supports: OpenAI, Gemini, Anthropic, and any OpenAI-compatible API.
     */
    public function up(): void
    {
        Schema::table('bots', function (Blueprint $table) {
            // Which AI provider to use: openai, gemini, anthropic, openai_compatible
            $table->string('ai_provider')->default('openai')->after('model_type');

            // The API key for the selected provider (encrypted at rest)
            $table->text('api_key_encrypted')->nullable()->after('ai_provider');

            // For openai_compatible providers: the base URL of the API
            $table->string('api_base_url')->nullable()->after('api_key_encrypted');

            // Bot persona and tone
            $table->string('bot_tone')->default('friendly')->after('system_prompt');
            $table->text('welcome_message')->nullable()->after('bot_tone');

            // Max tokens for AI response
            $table->integer('max_tokens')->default(500)->after('welcome_message');

            // Temperature for AI creativity (0.0 = deterministic, 1.0 = creative)
            $table->decimal('temperature', 3, 2)->default(0.70)->after('max_tokens');
        });
    }

    public function down(): void
    {
        Schema::table('bots', function (Blueprint $table) {
            $table->dropColumn([
                'ai_provider', 'api_key_encrypted', 'api_base_url',
                'bot_tone', 'welcome_message', 'max_tokens', 'temperature',
            ]);
        });
    }
};
