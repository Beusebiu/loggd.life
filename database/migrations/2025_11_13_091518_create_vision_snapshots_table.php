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
        Schema::create('vision_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Vision Content Fields (snapshot of vision at a point in time)
            $table->text('eulogy_method')->nullable();
            $table->json('bucket_list')->nullable();
            $table->text('mission_prompt')->nullable();
            $table->json('definition_of_success')->nullable();
            $table->json('odyssey_plan')->nullable();
            $table->json('future_calendar')->nullable();

            // Privacy & Social
            $table->boolean('is_public')->default(true);
            $table->json('private_sections')->nullable();

            // Snapshot metadata
            $table->timestamp('snapshot_date');
            $table->text('note')->nullable(); // "What changed" note from user

            $table->timestamps();

            // Index for fetching user's snapshots chronologically
            $table->index(['user_id', 'snapshot_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vision_snapshots');
    }
};
