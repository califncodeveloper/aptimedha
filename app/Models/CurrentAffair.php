<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CurrentAffair extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'current_affairs';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'year',
        'month',
        'description',
        'pdf_path',
        'pdf_name',
        'preview_image',
        'preview_image_alt',
        'file_size_bytes',
        'downloads_count',
        'is_published',
        'published_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
        'file_size_bytes' => 'integer',
        'downloads_count' => 'integer',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Array mapping of month numbers (1-12) to English month names.
     *
     * @return array<int, string>
     */
    public static function getMonths(): array
    {
        return [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ];
    }

    /**
     * Convert any month identifier (name, abbreviation, or string integer) to a 1-12 integer.
     */
    public static function monthNameToNumber(?string $month): ?int
    {
        if ($month === null || $month === '') {
            return null;
        }

        if (is_numeric($month)) {
            $val = (int) $month;
            return ($val >= 1 && $val <= 12) ? $val : null;
        }

        $cleaned = strtolower(trim($month));

        foreach (self::getMonths() as $num => $name) {
            if (strtolower($name) === $cleaned || strtolower(substr($name, 0, 3)) === $cleaned) {
                return $num;
            }
        }

        return null;
    }

    /**
     * Scope to published records only.
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
     * Scope to sort chronologically descending (newest month/year first).
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('year', 'desc')
            ->orderBy('month', 'desc');
    }

    /**
     * Scope filter by specific year.
     */
    public function scopeYear(Builder $query, ?int $year): Builder
    {
        if (empty($year)) {
            return $query;
        }

        return $query->where('year', $year);
    }

    /**
     * Scope filter by specific month number.
     */
    public function scopeMonth(Builder $query, ?int $month): Builder
    {
        if (empty($month)) {
            return $query;
        }

        return $query->where('month', $month);
    }

    /**
     * Scope search query by title or description.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (empty($term)) {
            return $query;
        }

        $term = '%' . trim($term) . '%';

        return $query->where(function (Builder $q) use ($term) {
            $q->where('title', 'like', $term)
              ->orWhere('description', 'like', $term);
        });
    }

    /**
     * Check if a valid PDF path exists.
     */
    public function hasPdf(): bool
    {
        return ! empty($this->pdf_path);
    }

    /**
     * Check if a preview image exists.
     */
    public function hasPreviewImage(): bool
    {
        return ! empty($this->preview_image);
    }

    /**
     * Accessor for month name.
     */
    public function getMonthNameAttribute(): string
    {
        return self::getMonths()[$this->month] ?? 'Month ' . $this->month;
    }

    /**
     * Accessor for full publication month and year label (e.g. "November 2026").
     */
    public function getPeriodLabelAttribute(): string
    {
        return $this->month_name . ' ' . $this->year;
    }

    /**
     * Accessor for formatted human-readable file size.
     */
    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size_bytes;

        if (! $bytes || $bytes <= 0) {
            return 'PDF Document';
        }

        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }

        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }

        return $bytes . ' B';
    }

    /**
     * Generate safe download filename for browser downloads.
     */
    public function getPdfDownloadFilename(): string
    {
        if (! empty($this->pdf_name)) {
            return Str::endsWith(strtolower($this->pdf_name), '.pdf')
                ? $this->pdf_name
                : $this->pdf_name . '.pdf';
        }

        return Str::slug('aptimedha-current-affairs-' . $this->month_name . '-' . $this->year) . '.pdf';
    }

    /**
     * Accessor for full preview image URL.
     */
    public function getPreviewImageUrlAttribute(): ?string
    {
        if (empty($this->preview_image)) {
            return null;
        }

        if (str_starts_with($this->preview_image, 'http://') || str_starts_with($this->preview_image, 'https://')) {
            return $this->preview_image;
        }

        if (str_starts_with($this->preview_image, '/')) {
            return asset(ltrim($this->preview_image, '/'));
        }

        if (Storage::disk('public')->exists($this->preview_image)) {
            return Storage::disk('public')->url($this->preview_image);
        }

        return asset($this->preview_image);
    }

    /**
     * Accessor for PDF delivery URL (direct link or fallback).
     */
    public function getPdfUrlAttribute(): ?string
    {
        if (empty($this->pdf_path)) {
            return null;
        }

        if (str_starts_with($this->pdf_path, 'http://') || str_starts_with($this->pdf_path, 'https://')) {
            return $this->pdf_path;
        }

        if (Storage::disk('public')->exists($this->pdf_path)) {
            return Storage::disk('public')->url($this->pdf_path);
        }

        return asset($this->pdf_path);
    }

    /**
     * URL for public preview action.
     */
    public function getPreviewUrlAttribute(): string
    {
        return route('current-affairs.preview', $this->id);
    }

    /**
     * URL for public download action.
     */
    public function getDownloadUrlAttribute(): string
    {
        return route('current-affairs.download', $this->id);
    }

    /**
     * URL for monthly issue page.
     */
    public function getPublicUrlAttribute(): string
    {
        return route('current-affairs.show', [
            'year' => $this->year,
            'month' => strtolower($this->month_name),
        ]);
    }
}

