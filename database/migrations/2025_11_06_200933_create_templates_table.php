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
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('category'); // 'vision', 'goal', 'review', 'custom'
            $table->string('behavior'); // 'singleton', 'versioned', 'recurring'
            $table->string('period')->nullable(); // '3-year', 'yearly', 'quarterly', 'weekly', 'daily', 'once'
            $table->json('structure'); // Array of fields/questions
            $table->string('icon')->nullable();
            $table->boolean('is_system')->default(false);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('templates');
    }
};
