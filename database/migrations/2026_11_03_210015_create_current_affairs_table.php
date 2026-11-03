<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create current_affairs table for monthly PDF issues.
     * Unique constraint on (year, month) prevents duplicate entries (SRS Section 8).
     */
    public function up(): void
    {
        Schema::create('current_affairs', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->text('description')->nullable();
            $table->string('pdf_path', 255);
            $table->string('preview_image', 255)->nullable();
            $table->unsignedBigInteger('file_size_bytes')->nullable();
            $table->unsignedInteger('downloads_count')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->unique(['year', 'month']);
            $table->index(['is_published', 'year', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('current_affairs');
    }
};

