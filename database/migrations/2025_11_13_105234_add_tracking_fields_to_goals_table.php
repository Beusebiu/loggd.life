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
        Schema::table('goals', function (Blueprint $table) {
            // Tracking type
            $table->enum('tracking_type', [
                'metric',      // Track a number over time (mortgage, income, weight)
                'milestone',   // Track completion of key checkpoints
                'evolution',   // Journal-style updates
                'active',      // Ongoing, no end date
            ])->default('milestone')->after('time_horizon');

            // For metric-based goals
            $table->string('metric_unit')->nullable()->after('tracking_type'); // '€', 'kg', 'customers'
            $table->decimal('metric_start_value', 12, 2)->nullable()->after('metric_unit');
            $table->decimal('metric_target_value', 12, 2)->nullable()->after('metric_start_value');
            $table->decimal('metric_current_value', 12, 2)->nullable()->after('metric_target_value');
            $table->boolean('metric_decrease')->default(false)->after('metric_current_value'); // true for mortgage (decrease is good)

            // Period identifier for quarterly/yearly goals
            $table->string('period_identifier')->nullable()->after('metric_decrease'); // 'Q1_2025', '2025', '2025-2028'

            // Optional parent goal (for hierarchy)
            $table->foreignId('parent_goal_id')->nullable()->after('period_identifier')->constrained('goals')->onDelete('set null');

            // Goal evolution tracking
            $table->text('last_update_note')->nullable()->after('notes'); // Latest journal entry
            $table->timestamp('last_reviewed_at')->nullable()->after('last_update_note');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goals', function (Blueprint $table) {
            $table->dropForeign(['parent_goal_id']);
            $table->dropColumn([
                'tracking_type',
                'metric_unit',
                'metric_start_value',
                'metric_target_value',
                'metric_current_value',
                'metric_decrease',
                'period_identifier',
                'parent_goal_id',
                'last_update_note',
                'last_reviewed_at',
            ]);
        });
    }
};
