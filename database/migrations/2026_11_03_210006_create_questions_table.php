<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create questions table with denormalized 4-option schema.
     * Stores question text, four options (with optional images), answer key,
     * explanation, difficulty and figure references for image-based questions.
     */
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained('topics')->restrictOnDelete();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->restrictOnDelete();
            $table->unsignedInteger('question_number');
            $table->unsignedInteger('section_number')->default(1);
            $table->text('question_text');
            $table->string('question_image', 255)->nullable();
            $table->string('question_image_alt', 255)->nullable();
            $table->text('option_a');
            $table->string('option_a_image', 255)->nullable();
            $table->text('option_b');
            $table->string('option_b_image', 255)->nullable();
            $table->text('option_c');
            $table->string('option_c_image', 255)->nullable();
            $table->text('option_d');
            $table->string('option_d_image', 255)->nullable();
            $table->enum('correct_option', ['A', 'B', 'C', 'D']);
            $table->longText('explanation')->nullable();
            $table->string('explanation_image', 255)->nullable();
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->string('figure_id', 60)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('views_count')->default(0);
            $table->timestamps();

            // Unique question numbering within a topic (SRS Section 9)
            $table->unique(['topic_id', 'question_number']);

            // Primary pagination index: topic + active + order
            $table->index(['topic_id', 'is_active', 'question_number']);

            // Section-based filtering index
            $table->index(['topic_id', 'section_number', 'is_active', 'question_number'], 'idx_questions_topic_section');

            // Category-level aggregation
            $table->index(['category_id', 'is_active']);

            // Figure cross-reference lookup
            $table->index('figure_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};

