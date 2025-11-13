<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_check_ins', function (Blueprint $table) {
            // Morning Planning Fields
            $table->boolean('yesterday_priority_completed')->nullable()->after('date');
            $table->text('yesterday_review')->nullable()->after('yesterday_priority_completed');
            $table->text('today_priority')->nullable()->after('yesterday_review');
            $table->boolean('goal_work_today')->default(false)->after('today_priority');
            $table->text('goal_work_details')->nullable()->after('goal_work_today');

            // Rename tomorrow_plan to challenges_learnings for clarity
            $table->renameColumn('tomorrow_plan', 'challenges_learnings');
        });
    }

    public function down(): void
    {
        Schema::table('daily_check_ins', function (Blueprint $table) {
            $table->dropColumn([
                'yesterday_priority_completed',
                'yesterday_review',
                'today_priority',
                'goal_work_today',
                'goal_work_details',
            ]);

            $table->renameColumn('challenges_learnings', 'tomorrow_plan');
        });
    }
};
