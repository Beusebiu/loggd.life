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
        Schema::create('goal_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goal_id')->constrained()->onDelete('cascade');

            // For metric goals - track value changes over time
            $table->decimal('metric_value', 12, 2)->nullable();

            // For all goals - journal entry
            $table->text('note')->nullable();

            // Milestone changes
            $table->json('milestones_snapshot')->nullable(); // Snapshot of milestone states at this update

            $table->timestamp('update_date'); // When this update was recorded
            $table->timestamps();

            // Indexes
            $table->index(['goal_id', 'update_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goal_updates');
    }
};
