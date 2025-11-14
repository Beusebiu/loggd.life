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
        Schema::create('user_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('activity_date');
            $table->string('activity_type'); // habit_check, daily_reflection, weekly_review, goal_milestone, goal_update, vision_update
            $table->foreignId('related_id')->nullable(); // ID of the related record (habit_id, goal_id, etc)
            $table->string('related_type')->nullable(); // Model class name for polymorphic relationship
            $table->integer('points_earned')->default(0); // Points earned for this activity
            $table->boolean('is_same_day')->default(true); // Was it logged on the same day?
            $table->json('metadata')->nullable(); // Additional context (e.g., habit count, milestone name)
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'activity_date']);
            $table->index(['user_id', 'activity_type']);
            $table->unique(['user_id', 'activity_date', 'activity_type', 'related_id'], 'unique_activity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_activity_logs');
    }
};
