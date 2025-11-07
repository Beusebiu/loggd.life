<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('habit_checks', function (Blueprint $table) {
            // Add time field for multiple checks (nullable for backward compatibility)
            $table->time('time')->nullable()->after('date');
        });

        // Drop foreign key temporarily to allow dropping the unique index
        Schema::table('habit_checks', function (Blueprint $table) {
            $table->dropForeign('habit_checks_habit_id_foreign');
        });

        // Drop unique constraint
        DB::statement('ALTER TABLE habit_checks DROP INDEX habit_checks_habit_id_date_unique');

        // Recreate foreign key
        Schema::table('habit_checks', function (Blueprint $table) {
            $table->foreign('habit_id', 'habit_checks_habit_id_foreign')
                ->references('id')
                ->on('habits')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign key
        Schema::table('habit_checks', function (Blueprint $table) {
            $table->dropForeign('habit_checks_habit_id_foreign');
        });

        // Restore unique constraint
        Schema::table('habit_checks', function (Blueprint $table) {
            $table->unique(['habit_id', 'date'], 'habit_checks_habit_id_date_unique');
        });

        // Recreate foreign key
        Schema::table('habit_checks', function (Blueprint $table) {
            $table->foreign('habit_id', 'habit_checks_habit_id_foreign')
                ->references('id')
                ->on('habits')
                ->cascadeOnDelete();
        });

        // Remove time field
        Schema::table('habit_checks', function (Blueprint $table) {
            $table->dropColumn('time');
        });
    }
};
