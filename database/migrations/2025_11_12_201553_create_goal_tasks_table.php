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
        Schema::create('goal_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goal_id')->constrained()->onDelete('cascade');
            $table->foreignId('milestone_id')->nullable()->constrained('goal_milestones')->onDelete('set null');
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('completed')->default(false);
            $table->date('due_date')->nullable();
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
        Schema::dropIfExists('goal_tasks');
    }
};
