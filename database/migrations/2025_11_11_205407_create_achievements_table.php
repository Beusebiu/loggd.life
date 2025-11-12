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
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('achievement_type'); // e.g., 'habit_completed', 'streak_3', 'streak_7', 'perfect_week', etc.
            $table->json('metadata')->nullable(); // Extra data like habit_id, streak_count, habit_name, etc.
            $table->timestamp('shown_at')->nullable(); // When the celebration was shown to the user
            $table->timestamps();

            $table->index(['user_id', 'shown_at']);
            $table->index(['user_id', 'achievement_type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achievements');
    }
};
