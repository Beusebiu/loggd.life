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
        Schema::create('goal_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goal_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->boolean('completed')->default(false);
            $table->date('target_date')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index(['goal_id', 'completed']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goal_milestones');
    }
};
