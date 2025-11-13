<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_check_ins', function (Blueprint $table) {
            // Add today's tasks list
            $table->json('today_tasks')->nullable()->after('today_priority');

            // Remove energy_level (keeping only productivity and mood)
            $table->dropColumn('energy_level');
        });
    }

    public function down(): void
    {
        Schema::table('daily_check_ins', function (Blueprint $table) {
            $table->dropColumn('today_tasks');
            $table->integer('energy_level')->nullable();
        });
    }
};
