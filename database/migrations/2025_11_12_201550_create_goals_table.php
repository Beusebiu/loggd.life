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
        Schema::create('goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Basic Info
            $table->string('title');
            $table->text('description')->nullable();

            // Categorization
            $table->enum('life_area', [
                'career',
                'health',
                'relationships',
                'financial',
                'growth',
                'impact',
                'other',
            ])->default('other');

            $table->enum('time_horizon', [
                '3_year',
                'yearly',
                'quarterly',
                'monthly',
            ])->default('quarterly');

            // Tracking
            $table->enum('status', [
                'active',
                'completed',
                'paused',
                'abandoned',
            ])->default('active');

            $table->integer('progress_percentage')->default(0);
            $table->date('target_date')->nullable();
            $table->date('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            // Additional Data
            $table->text('notes')->nullable();
            $table->json('custom_fields')->nullable(); // For any user-defined fields

            // Social & Privacy
            $table->boolean('is_public')->default(true);
            $table->boolean('allow_comments')->default(true);
            $table->integer('celebration_count')->default(0);

            // Connection to Vision
            $table->string('vision_life_area')->nullable(); // Which life area from vision this relates to

            // Order (for user-defined ordering)
            $table->integer('order')->default(0);

            $table->timestamps();
            $table->softDeletes(); // Allow soft deletes for abandoned goals

            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'time_horizon']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goals');
    }
};
