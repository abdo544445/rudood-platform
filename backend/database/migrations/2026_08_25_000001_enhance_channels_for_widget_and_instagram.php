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
        Schema::table('channels', function (Blueprint $table) {
            // Channel Activation Toggle
            if (!Schema::hasColumn('channels', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('is_connected');
            }

            // Instagram Direct & Comments
            if (!Schema::hasColumn('channels', 'instagram_account_id')) {
                $table->string('instagram_account_id')->nullable()->after('chat_id');
            }
            if (!Schema::hasColumn('channels', 'page_access_token')) {
                $table->text('page_access_token')->nullable()->after('instagram_account_id');
            }
            if (!Schema::hasColumn('channels', 'auto_reply_comments')) {
                $table->boolean('auto_reply_comments')->default(false)->after('page_access_token');
            }
            if (!Schema::hasColumn('channels', 'comment_reply_template')) {
                $table->text('comment_reply_template')->nullable()->after('auto_reply_comments');
            }

            // Web Live Chat Widget Customizations
            if (!Schema::hasColumn('channels', 'widget_color')) {
                $table->string('widget_color')->default('#d4af37')->after('comment_reply_template');
            }
            if (!Schema::hasColumn('channels', 'widget_position')) {
                $table->string('widget_position')->default('right')->after('widget_color');
            }
            if (!Schema::hasColumn('channels', 'widget_greeting')) {
                $table->text('widget_greeting')->nullable()->after('widget_position');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('channels', function (Blueprint $table) {
            $table->dropColumn([
                'is_active',
                'instagram_account_id',
                'page_access_token',
                'auto_reply_comments',
                'comment_reply_template',
                'widget_color',
                'widget_position',
                'widget_greeting',
            ]);
        });
    }
};
