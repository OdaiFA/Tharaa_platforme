<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enrollment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'course_id',
        'status',
        'progress_percentage',
        'enrolled_at',
        'completed_at',
        'certificate_issued',
        'certificate_url',
    ];

    protected function casts(): array
    {
        return [
            'progress_percentage' => 'integer',
            'enrolled_at' => 'datetime',
            'completed_at' => 'datetime',
            'certificate_issued' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function lessonProgress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function scopeForUser(Builder $query, ?int $userId = null): Builder
    {
        return $query->where('user_id', $userId ?? auth()->id());
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    public function getProgressPercentageAttribute(int $value): int
    {
        return (int) $value;
    }
}
