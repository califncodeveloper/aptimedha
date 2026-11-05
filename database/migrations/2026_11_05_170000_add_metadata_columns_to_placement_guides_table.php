<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the additive migration to support SEO, asset metadata, and publication dates.
     */
    public function up(): void
    {
        if (Schema::hasTable('placement_guides')) {
            Schema::table('placement_guides', function (Blueprint $table) {
                if (! Schema::hasColumn('placement_guides', 'featured_image_alt')) {
                    $table->string('featured_image_alt', 255)->nullable()->after('featured_image');
                }
                if (! Schema::hasColumn('placement_guides', 'downloadable_asset_name')) {
                    $table->string('downloadable_asset_name', 255)->nullable()->after('downloadable_asset');
                }
                if (! Schema::hasColumn('placement_guides', 'downloadable_asset_mime')) {
                    $table->string('downloadable_asset_mime', 100)->nullable()->after('downloadable_asset_name');
                }
                if (! Schema::hasColumn('placement_guides', 'published_at')) {
                    $table->timestamp('published_at')->nullable()->after('is_published');
                }
                if (! Schema::hasColumn('placement_guides', 'meta_title')) {
                    $table->string('meta_title', 255)->nullable()->after('published_at');
                }
                if (! Schema::hasColumn('placement_guides', 'meta_description')) {
                    $table->text('meta_description')->nullable()->after('meta_title');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('placement_guides')) {
            Schema::table('placement_guides', function (Blueprint $table) {
                $columns = [
                    'meta_description',
                    'meta_title',
                    'published_at',
                    'downloadable_asset_mime',
                    'downloadable_asset_name',
                    'featured_image_alt',
                ];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('placement_guides', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};

