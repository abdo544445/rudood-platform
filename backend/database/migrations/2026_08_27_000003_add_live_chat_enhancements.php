<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for Live Chat Enhancements (Media metadata & CSAT).
     */
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            if (!Schema::hasColumn('messages', 'file_name')) {
                $table->string('file_name', 255)->nullable()->after('media_url');
            }
            if (!Schema::hasColumn('messages', 'file_size')) {
                $table->unsignedBigInteger('file_size')->nullable()->after('file_name');
            }
        });

        Schema::table('conversations', function (Blueprint $table) {
            if (!Schema::hasColumn('conversations', 'csat_score')) {
                $table->tinyInteger('csat_score')->nullable()->after('tags');
            }
            if (!Schema::hasColumn('conversations', 'csat_feedback')) {
                $table->text('csat_feedback')->nullable()->after('csat_score');
            }
            if (!Schema::hasColumn('conversations', 'resolved_at')) {
                $table->timestamp('resolved_at')->nullable()->after('csat_feedback');
            }
            if (!Schema::hasColumn('conversations', 'resolved_by')) {
                $table->string('resolved_by', 100)->nullable()->after('resolved_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            if (Schema::hasColumn('messages', 'file_size')) {
                $table->dropColumn('file_size');
            }
            if (Schema::hasColumn('messages', 'file_name')) {
                $table->dropColumn('file_name');
            }
        });

        Schema::table('conversations', function (Blueprint $table) {
            if (Schema::hasColumn('conversations', 'resolved_by')) {
                $table->dropColumn('resolved_by');
            }
            if (Schema::hasColumn('conversations', 'resolved_at')) {
                $table->dropColumn('resolved_at');
            }
            if (Schema::hasColumn('conversations', 'csat_feedback')) {
                $table->dropColumn('csat_feedback');
            }
            if (Schema::hasColumn('conversations', 'csat_score')) {
                $table->dropColumn('csat_score');
            }
        });
    }
};
