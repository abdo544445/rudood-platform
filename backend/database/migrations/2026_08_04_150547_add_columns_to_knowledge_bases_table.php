<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('knowledge_bases', function (Blueprint $table) {
            $table->foreignId('bot_id')->constrained()->onDelete('cascade')->after('id');
            $table->string('file_name')->after('bot_id');
            $table->string('file_path')->nullable()->after('file_name');
            $table->longText('document_text')->nullable()->after('file_path');
            $table->string('status')->default('processed')->after('document_text');
        });
    }

    public function down(): void
    {
        Schema::table('knowledge_bases', function (Blueprint $table) {
            $table->dropForeign(['bot_id']);
            $table->dropColumn(['bot_id', 'file_name', 'file_path', 'document_text', 'status']);
        });
    }
};
