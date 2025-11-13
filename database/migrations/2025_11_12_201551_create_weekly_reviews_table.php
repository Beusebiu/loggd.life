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
        Schema::create('weekly_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('week_start_date');
            $table->date('week_end_date');

            // Auto-populated Stats
            $table->integer('total_check_ins')->default(0); // Out of 7
            $table->json('completed_goals')->nullable(); // Goal IDs with progress
            $table->integer('total_wins')->default(0);
            $table->decimal('avg_energy', 3, 1)->nullable();
            $table->decimal('avg_productivity', 3, 1)->nullable();
            $table->decimal('avg_mood', 3, 1)->nullable();

            // Goal Progress This Week
            $table->json('goal_progress')->nullable(); // { goal_id: { tasks_done, milestones_hit, progress_delta } }

            // Manual Reflection
            $table->text('biggest_win')->nullable();
            $table->text('biggest_challenge')->nullable();
            $table->text('what_i_learned')->nullable();
            $table->integer('vision_alignment')->nullable(); // 1-10 scale

            // Next Week Planning
            $table->json('next_week_goals')->nullable(); // Goal IDs to focus on
            $table->text('next_week_focus')->nullable();

            // Additional Notes
            $table->text('notes')->nullable();

            // Social
            $table->boolean('is_public')->default(false);

            $table->timestamps();

            // One review per week per user
            $table->unique(['user_id', 'week_start_date']);
            $table->index('week_start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekly_reviews');
    }
};
