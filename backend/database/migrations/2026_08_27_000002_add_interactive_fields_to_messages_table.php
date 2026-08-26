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
            if (!Schema::hasColumn('messages', 'interactive_type')) {
                $table->string('interactive_type', 50)->nullable()->after('media_url'); // button, list, carousel
            }
            if (!Schema::hasColumn('messages', 'interactive_data')) {
                $table->longText('interactive_data')->nullable()->after('interactive_type'); // JSON array of buttons/items
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            if (Schema::hasColumn('messages', 'interactive_type')) {
                $table->dropColumn(['interactive_type', 'interactive_data']);
            }
        });
    }
};
