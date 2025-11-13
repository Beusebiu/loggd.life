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
        Schema::create('daily_check_ins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date');

            // Core Fields
            $table->json('quick_wins')->nullable(); // Array of wins
            $table->text('challenges')->nullable();
            $table->text('tomorrow_plan')->nullable();
            $table->text('gratitude')->nullable();

            // Goal Integration
            $table->json('goals_worked_on')->nullable(); // Array of goal IDs
            $table->json('goal_specific_tasks')->nullable(); // { goal_id: [tasks] }

            // Habit Integration (links to habits table)
            $table->json('habit_completions')->nullable(); // Array of habit IDs completed

            // Metrics
            $table->integer('energy_level')->nullable(); // 1-10
            $table->integer('productivity')->nullable(); // 1-10
            $table->integer('mood')->nullable(); // 1-10

            // Free Form
            $table->text('notes')->nullable();

            // Social
            $table->boolean('is_public')->default(false);
            $table->boolean('share_wins_to_feed')->default(false);

            $table->timestamps();

            // One check-in per day per user
            $table->unique(['user_id', 'date']);
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_check_ins');
    }
};
