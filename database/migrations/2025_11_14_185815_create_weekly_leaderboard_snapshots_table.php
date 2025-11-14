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
        Schema::create('weekly_leaderboard_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('week_start_date');
            $table->integer('weekly_points')->default(0);
            $table->integer('rank')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'week_start_date']);
            $table->index(['week_start_date', 'rank']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekly_leaderboard_snapshots');
    }
};
