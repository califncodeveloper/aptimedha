<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the additive migration to support publication date, friendly PDF name, and preview image alt text.
     */
    public function up(): void
    {
        if (Schema::hasTable('current_affairs')) {
            Schema::table('current_affairs', function (Blueprint $table) {
                if (! Schema::hasColumn('current_affairs', 'pdf_name')) {
                    $table->string('pdf_name', 255)->nullable()->after('pdf_path');
                }
                if (! Schema::hasColumn('current_affairs', 'preview_image_alt')) {
                    $table->string('preview_image_alt', 255)->nullable()->after('preview_image');
                }
                if (! Schema::hasColumn('current_affairs', 'published_at')) {
                    $table->timestamp('published_at')->nullable()->after('is_published');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('current_affairs')) {
            Schema::table('current_affairs', function (Blueprint $table) {
                $columns = ['published_at', 'preview_image_alt', 'pdf_name'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('current_affairs', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};

