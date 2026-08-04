<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add a human-readable `question` field to auto_rules
     * so each FAQ entry stores the question explicitly.
     */
    public function up(): void
    {
        Schema::table('auto_rules', function (Blueprint $table) {
            // The question/trigger phrase displayed in the UI
            $table->string('question')->nullable()->after('workspace_id');
        });
    }

    public function down(): void
    {
        Schema::table('auto_rules', function (Blueprint $table) {
            $table->dropColumn('question');
        });
    }
};
