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
        if (!Schema::hasTable('subscriber_requests')) {
            Schema::create('subscriber_requests', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->index();
                $table->string('phone');
                $table->string('company_name')->nullable();
                $table->string('selected_plan')->default('professional');
                $table->text('notes')->nullable();
                $table->string('status')->default('pending'); // pending, contacted, approved, rejected
                $table->text('admin_notes')->nullable();
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->unsignedBigInteger('created_user_id')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriber_requests');
    }
};
