<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('canned_replies')) {
            Schema::create('canned_replies', function (Blueprint $table) {
                $table->id();
                $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
                $table->string('shortcut'); // e.g. /iban, /shipping, /return, /discount
                $table->string('title');    // e.g. تفاصيل الحساب البنكي
                $table->text('content');     // The message body
                $table->timestamps();

                $table->index(['workspace_id', 'shortcut']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('canned_replies');
    }
};
