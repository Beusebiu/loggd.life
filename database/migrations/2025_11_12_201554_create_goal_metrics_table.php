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
        Schema::create('goal_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goal_id')->constrained()->onDelete('cascade');
            $table->string('name'); // e.g., "Monthly Revenue", "Weight", "Users"
            $table->decimal('target_value', 15, 2);
            $table->decimal('current_value', 15, 2)->default(0);
            $table->string('unit')->nullable(); // e.g., "$", "kg", "users"
            $table->enum('direction', ['increase', 'decrease'])->default('increase'); // Should current increase or decrease toward target?
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index('goal_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goal_metrics');
    }
};
