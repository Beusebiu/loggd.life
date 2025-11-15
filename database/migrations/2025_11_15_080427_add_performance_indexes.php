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
        // Add indexes to users table for leaderboard performance
        Schema::table('users', function (Blueprint $table) {
            $table->index('total_points', 'users_total_points_index');
            $table->index('weekly_points', 'users_weekly_points_index');
            $table->index('last_activity_at', 'users_last_activity_at_index');
        });

        // Add composite index to habit_checks for efficient date filtering
        Schema::table('habit_checks', function (Blueprint $table) {
            $table->index(['habit_id', 'date'], 'habit_checks_habit_id_date_index');
        });

        // Add index to goals for parent-child relationships
        Schema::table('goals', function (Blueprint $table) {
            $table->index('parent_goal_id', 'goals_parent_goal_id_index');
        });

        // Add composite index to weekly_leaderboard_snapshots
        Schema::table('weekly_leaderboard_snapshots', function (Blueprint $table) {
            $table->index(['user_id', 'week_start_date'], 'weekly_snapshots_user_week_index');
        });

        // Add index to user_activity_logs for gamification queries
        Schema::table('user_activity_logs', function (Blueprint $table) {
            $table->index(['user_id', 'activity_date'], 'user_activity_logs_user_date_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_total_points_index');
            $table->dropIndex('users_weekly_points_index');
            $table->dropIndex('users_last_activity_at_index');
        });

        Schema::table('habit_checks', function (Blueprint $table) {
            $table->dropIndex('habit_checks_habit_id_date_index');
        });

        Schema::table('goals', function (Blueprint $table) {
            $table->dropIndex('goals_parent_goal_id_index');
        });

        Schema::table('weekly_leaderboard_snapshots', function (Blueprint $table) {
            $table->dropIndex('weekly_snapshots_user_week_index');
        });

        Schema::table('user_activity_logs', function (Blueprint $table) {
            $table->dropIndex('user_activity_logs_user_date_index');
        });
    }
};
