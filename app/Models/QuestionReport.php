<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionReport extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'question_reports';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'question_id',
        'user_id',
        'issue_type',
        'description',
        'status',
        'admin_notes',
        'resolved_by',
        'resolved_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'question_id' => 'integer',
        'user_id' => 'integer',
        'resolved_by' => 'integer',
        'resolved_at' => 'datetime',
    ];

    /**
     * Target question for the report.
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * User who reported the question.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Administrator who resolved the report.
     */
    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    /**
     * Human readable issue label.
     */
    public function getIssueLabelAttribute(): string
    {
        return match ($this->issue_type) {
            'incorrect_answer' => 'Incorrect Answer',
            'incorrect_explanation' => 'Incorrect Explanation',
            'wrong_question' => 'Incorrect Question',
            'broken_image' => 'Missing Image / Figure',
            'duplicate_question' => 'Duplicate Question',
            'typo_formatting' => 'Typo / Formatting',
            default => 'Other',
        };
    }

    /**
     * Status label formatting.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'new' => 'New',
            'under_review' => 'Under Review',
            'resolved' => 'Resolved',
            'closed' => 'Closed',
            default => ucfirst(str_replace('_', ' ', $this->status ?? 'new')),
        };
    }

    /**
     * Scope by status.
     */
    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }
}

