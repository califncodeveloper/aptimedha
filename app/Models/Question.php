<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Question extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'questions';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'topic_id',
        'category_id',
        'subject_id',
        'question_number',
        'section_number',
        'question_text',
        'question_image',
        'question_image_alt',
        'option_a',
        'option_a_image',
        'option_b',
        'option_b_image',
        'option_c',
        'option_c_image',
        'option_d',
        'option_d_image',
        'correct_option',
        'explanation',
        'explanation_image',
        'difficulty',
        'figure_id',
        'is_active',
        'views_count',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'topic_id' => 'integer',
        'category_id' => 'integer',
        'subject_id' => 'integer',
        'question_number' => 'integer',
        'section_number' => 'integer',
        'is_active' => 'boolean',
        'views_count' => 'integer',
    ];

    /**
     * Category taxonomy relation.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Optional Subject taxonomy relation.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Topic taxonomy relation.
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    /**
     * Bookmarks relation.
     */
    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    /**
     * Discussions relation.
     */
    public function discussions(): HasMany
    {
        return $this->hasMany(Discussion::class);
    }

    /**
     * Question reports relation.
     */
    public function reports(): HasMany
    {
        return $this->hasMany(QuestionReport::class);
    }

    /**
     * Check if a specific user bookmarked this question.
     */
    public function isBookmarkedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return $this->bookmarks()->where('user_id', $user->id)->exists();
    }

    /**
     * Scope a query to only include active questions.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Alias for question_text to maintain compatibility with client templates.
     */
    public function getQuestionAttribute(): string
    {
        return $this->question_text ?? '';
    }

    /**
     * Alias for question_number.
     */
    public function getNumberAttribute(): int
    {
        return (int) ($this->question_number ?? 0);
    }

    /**
     * Category name accessor.
     */
    public function getCategoryNameAttribute(): string
    {
        return $this->category?->name ?? '';
    }

    /**
     * Category slug accessor.
     */
    public function getCategorySlugAttribute(): string
    {
        return $this->category?->slug ?? '';
    }

    /**
     * Subject name accessor.
     */
    public function getSubjectNameAttribute(): ?string
    {
        return $this->subject?->name;
    }

    /**
     * Topic name accessor.
     */
    public function getTopicNameAttribute(): string
    {
        return $this->topic?->name ?? '';
    }

    /**
     * Topic slug accessor.
     */
    public function getTopicSlugAttribute(): string
    {
        return $this->topic?->slug ?? '';
    }

    /**
     * Check if question has an image.
     */
    public function getHasImageAttribute(): bool
    {
        return !empty($this->question_image);
    }

    /**
     * Resolves public URL for question image.
     */
    public function getImageUrlAttribute(): ?string
    {
        if (empty($this->question_image)) {
            return null;
        }

        if (str_starts_with($this->question_image, 'http://') || str_starts_with($this->question_image, 'https://')) {
            return $this->question_image;
        }

        if (file_exists(public_path($this->question_image))) {
            return asset($this->question_image);
        }

        return Storage::disk('public')->url($this->question_image);
    }

    /**
     * Normalized 4 options array.
     *
     * @return array<string, string>
     */
    public function getOptionsAttribute(): array
    {
        return [
            'A' => $this->option_a ?? '',
            'B' => $this->option_b ?? '',
            'C' => $this->option_c ?? '',
            'D' => $this->option_d ?? '',
        ];
    }

    /**
     * Discussions count accessor fallback.
     */
    public function getDiscussionsCountAttribute(): int
    {
        return (int) ($this->attributes['discussions_count'] ?? 0);
    }

    /**
     * Bookmarks count accessor fallback.
     */
    public function getBookmarksCountAttribute(): int
    {
        return (int) ($this->attributes['bookmarks_count'] ?? 0);
    }
}
