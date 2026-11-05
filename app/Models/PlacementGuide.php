<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PlacementGuide extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'placement_guides';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'slug',
        'category',
        'excerpt',
        'content',
        'featured_image',
        'featured_image_alt',
        'downloadable_asset',
        'downloadable_asset_name',
        'downloadable_asset_mime',
        'display_order',
        'is_published',
        'published_at',
        'meta_title',
        'meta_description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_published' => 'boolean',
        'display_order' => 'integer',
        'published_at' => 'datetime',
    ];

    /**
     * Standard categories for Placement Preparation.
     *
     * @return array<string, string>
     */
    public static function getCategories(): array
    {
        return [
            'roadmap' => 'Placement Roadmap',
            'resume' => 'Resume & Profile',
            'aptitude' => 'Aptitude & Assessments',
            'technical' => 'Technical & Coding',
            'interview' => 'Interviews & GD',
            'company' => 'Company Preparation',
            'general' => 'General Guidance',
        ];
    }

    /**
     * Retrieve the model for a bound value (supports ID or slug).
     *
     * @param  mixed  $value
     * @param  string|null  $field
     */
    public function resolveRouteBinding($value, $field = null): ?Model
    {
        return $this->where('slug', $value)
            ->orWhere('id', is_numeric($value) ? (int) $value : 0)
            ->firstOrFail();
    }

    /**
     * Scope to published guides only.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)
            ->where(function (Builder $q) {
                $q->whereNull('published_at')
                  ->orWhere('published_at', '<=', now());
            });
    }

    /**
     * Scope to ordered guides.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('display_order', 'asc')
            ->orderBy('id', 'asc');
    }

    /**
     * Scope filter by category.
     */
    public function scopeCategory(Builder $query, ?string $category): Builder
    {
        if (empty($category)) {
            return $query;
        }

        return $query->where('category', $category);
    }

    /**
     * Scope search query by title, excerpt, or content.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (empty($term)) {
            return $query;
        }

        $term = '%' . trim($term) . '%';

        return $query->where(function (Builder $q) use ($term) {
            $q->where('title', 'like', $term)
              ->orWhere('excerpt', 'like', $term)
              ->orWhere('content', 'like', $term);
        });
    }

    /**
     * Check if a valid downloadable asset is present.
     */
    public function hasDownloadableAsset(): bool
    {
        return ! empty($this->downloadable_asset);
    }

    /**
     * Human-readable category label.
     */
    public function categoryLabel(): string
    {
        $categories = self::getCategories();

        return $categories[$this->category] ?? ucfirst($this->category ?: 'Guide');
    }

    /**
     * Category badge CSS styling.
     */
    public function categoryBadgeClass(): string
    {
        return match ($this->category) {
            'roadmap' => 'bg-blue-50 text-[#153B70] border-blue-200',
            'resume' => 'bg-teal-50 text-[#087F82] border-teal-200',
            'aptitude' => 'bg-amber-50 text-amber-800 border-amber-200',
            'technical' => 'bg-indigo-50 text-indigo-800 border-indigo-200',
            'interview' => 'bg-purple-50 text-purple-800 border-purple-200',
            'company' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
            default => 'bg-gray-100 text-[#243247] border-gray-200',
        };
    }

    /**
     * Accessor for full featured image URL.
     */
    public function getFeaturedImageUrlAttribute(): ?string
    {
        if (empty($this->featured_image)) {
            return null;
        }

        if (str_starts_with($this->featured_image, 'http://') || str_starts_with($this->featured_image, 'https://')) {
            return $this->featured_image;
        }

        if (str_starts_with($this->featured_image, '/')) {
            return asset(ltrim($this->featured_image, '/'));
        }

        if (Storage::disk('public')->exists($this->featured_image)) {
            return Storage::disk('public')->url($this->featured_image);
        }

        return asset($this->featured_image);
    }

    /**
     * Accessor for downloadable asset URL.
     */
    public function getDownloadUrlAttribute(): ?string
    {
        if (! $this->hasDownloadableAsset()) {
            return null;
        }

        return route('placement.download', $this->slug);
    }

    /**
     * Generate a unique slug for a placement guide.
     */
    public static function generateUniqueSlug(string $title, ?int $excludeId = null): string
    {
        $slug = Str::slug($title);
        $original = $slug;
        $counter = 2;

        while (static::where('slug', $slug)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->exists()) {
            $slug = "{$original}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}

