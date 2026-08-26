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
        // 1. Add attribution columns to mock_orders table
        Schema::table('mock_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('mock_orders', 'conversation_id')) {
                $table->unsignedBigInteger('conversation_id')->nullable()->index()->after('workspace_id');
            }
            if (!Schema::hasColumn('mock_orders', 'is_attributed_to_bot')) {
                $table->boolean('is_attributed_to_bot')->default(false)->after('conversation_id');
            }
            if (!Schema::hasColumn('mock_orders', 'attribution_type')) {
                $table->string('attribution_type')->nullable()->after('is_attributed_to_bot');
            }
            if (!Schema::hasColumn('mock_orders', 'attribution_confidence')) {
                $table->decimal('attribution_confidence', 4, 2)->default(1.00)->after('attribution_type');
            }
        });

        // 2. Add conversion outcome columns to conversations table
        Schema::table('conversations', function (Blueprint $table) {
            if (!Schema::hasColumn('conversations', 'is_converted')) {
                $table->boolean('is_converted')->default(false)->after('csat_feedback');
            }
            if (!Schema::hasColumn('conversations', 'conversion_revenue')) {
                $table->decimal('conversion_revenue', 10, 2)->default(0.00)->after('is_converted');
            }
            if (!Schema::hasColumn('conversations', 'converted_at')) {
                $table->timestamp('converted_at')->nullable()->after('conversion_revenue');
            }
            if (!Schema::hasColumn('conversations', 'attributed_order_id')) {
                $table->unsignedBigInteger('attributed_order_id')->nullable()->after('converted_at');
            }
        });

        // 3. Enhance analytics_snapshots table for monthly deflection and ROI storage
        Schema::table('analytics_snapshots', function (Blueprint $table) {
            if (!Schema::hasColumn('analytics_snapshots', 'workspace_id')) {
                $table->unsignedBigInteger('workspace_id')->nullable()->index()->after('id');
            }
            if (!Schema::hasColumn('analytics_snapshots', 'period_key')) {
                $table->string('period_key')->nullable()->index()->after('workspace_id'); // e.g. '2026-08'
            }
            if (!Schema::hasColumn('analytics_snapshots', 'total_conversations')) {
                $table->integer('total_conversations')->default(0);
            }
            if (!Schema::hasColumn('analytics_snapshots', 'ai_resolved_conversations')) {
                $table->integer('ai_resolved_conversations')->default(0);
            }
            if (!Schema::hasColumn('analytics_snapshots', 'deflection_rate')) {
                $table->decimal('deflection_rate', 5, 2)->default(0.00);
            }
            if (!Schema::hasColumn('analytics_snapshots', 'hours_saved')) {
                $table->decimal('hours_saved', 8, 2)->default(0.00);
            }
            if (!Schema::hasColumn('analytics_snapshots', 'cost_savings_amount')) {
                $table->decimal('cost_savings_amount', 10, 2)->default(0.00);
            }
            if (!Schema::hasColumn('analytics_snapshots', 'revenue_generated')) {
                $table->decimal('revenue_generated', 12, 2)->default(0.00);
            }
            if (!Schema::hasColumn('analytics_snapshots', 'converted_orders_count')) {
                $table->integer('converted_orders_count')->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mock_orders', function (Blueprint $table) {
            $table->dropColumn([
                'conversation_id',
                'is_attributed_to_bot',
                'attribution_type',
                'attribution_confidence',
            ]);
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->dropColumn([
                'is_converted',
                'conversion_revenue',
                'converted_at',
                'attributed_order_id',
            ]);
        });

        Schema::table('analytics_snapshots', function (Blueprint $table) {
            $table->dropColumn([
                'workspace_id',
                'period_key',
                'total_conversations',
                'ai_resolved_conversations',
                'deflection_rate',
                'hours_saved',
                'cost_savings_amount',
                'revenue_generated',
                'converted_orders_count',
            ]);
        });
    }
};
