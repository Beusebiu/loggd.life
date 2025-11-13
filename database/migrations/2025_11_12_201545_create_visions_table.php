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
        Schema::create('visions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Vision Content Fields (based on user's structure)
            $table->text('eulogy_method')->nullable(); // 📜 The Eulogy Method
            $table->json('bucket_list')->nullable(); // 🪣 The Bucket List (array of items)
            $table->text('mission_prompt')->nullable(); // 🎯 The Mission Prompt
            $table->json('definition_of_success')->nullable(); // 🏆 Definition of Success (structured fields)
            $table->json('odyssey_plan')->nullable(); // 🗺️ Odyssey Plan (3-5 life paths)
            $table->json('future_calendar')->nullable(); // 📅 Future Calendar (timeline of future events)

            // Privacy & Social
            $table->boolean('is_public')->default(true);
            $table->json('private_sections')->nullable(); // Array of section names that are private

            $table->timestamps();

            // Ensure one vision per user
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visions');
    }
};
