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
        Schema::table('daily_check_ins', function (Blueprint $table) {
            // Add simplified combined reflection field
            $table->text('day_reflection')->nullable()->after('goal_work_details');

            // Remove separate evening reflection fields
            $table->dropColumn([
                'quick_wins',
                'challenges',
                'challenges_learnings',
                'gratitude',
                'productivity',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_check_ins', function (Blueprint $table) {
            $table->dropColumn('day_reflection');

            $table->json('quick_wins')->nullable();
            $table->text('challenges')->nullable();
            $table->text('challenges_learnings')->nullable();
            $table->text('gratitude')->nullable();
            $table->integer('productivity')->nullable();
        });
    }
};
