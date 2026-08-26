<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workspaces', function (Blueprint $table) {
            if (!Schema::hasColumn('workspaces', 'monthly_message_limit')) {
                $table->unsignedInteger('monthly_message_limit')->default(2000)->after('allow_custom_api_key');
            }
            if (!Schema::hasColumn('workspaces', 'messages_used_this_month')) {
                $table->unsignedInteger('messages_used_this_month')->default(0)->after('monthly_message_limit');
            }
            if (!Schema::hasColumn('workspaces', 'tokens_used_this_month')) {
                $table->unsignedBigInteger('tokens_used_this_month')->default(0)->after('messages_used_this_month');
            }
        });
    }

    public function down(): void
    {
        Schema::table('workspaces', function (Blueprint $table) {
            $table->dropColumn([
                'monthly_message_limit',
                'messages_used_this_month',
                'tokens_used_this_month',
            ]);
        });
    }
};
