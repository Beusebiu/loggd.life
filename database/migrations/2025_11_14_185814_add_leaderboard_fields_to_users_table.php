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
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_activity_at')->nullable()->after('updated_at');
            $table->boolean('comeback_multiplier_active')->default(false)->after('last_activity_at');
            $table->timestamp('comeback_multiplier_expires_at')->nullable()->after('comeback_multiplier_active');
            $table->integer('weekly_points')->default(0)->after('total_points');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['last_activity_at', 'comeback_multiplier_active', 'comeback_multiplier_expires_at', 'weekly_points']);
        });
    }
};
