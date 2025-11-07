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
        Schema::create('check_ins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['daily', 'weekly', 'monthly', 'quarterly', 'yearly', 'custom'])->default('daily');
            $table->date('date'); // The date/week/month it represents
            $table->json('content'); // Flexible structure for different templates (questions/answers)
            $table->boolean('is_public')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'type', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('check_ins');
    }
};
