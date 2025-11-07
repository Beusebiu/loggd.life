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
        Schema::create('entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['task', 'habit', 'journal', 'video', 'goal_update'])->default('task');
            $table->text('content');
            $table->date('date'); // Can be different from created_at (for backdated entries)
            $table->boolean('is_public')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'date']);
            $table->index(['user_id', 'is_public']);
            $table->index('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entries');
    }
};
