<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Course extends Model
{
    use HasFactory, Searchable, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'level',
        'thumbnail',
        'duration_hours',
        'is_published',
        'certificate_enabled',
        'passing_score',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'duration_hours' => 'integer',
            'is_published' => 'boolean',
            'certificate_enabled' => 'boolean',
            'passing_score' => 'integer',
        ];
    }

    public function ageGroups(): BelongsToMany
    {
        return $this->belongsToMany(AgeGroup::class, 'course_age_group');
    }

    public function modules(): HasMany
    {
        return $this->hasMany(Module::class)->orderBy('order_index');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeForLevel(Builder $query, string $level): Builder
    {
        return $query->where('level', $level);
    }

    public function scopeForAgeGroup(Builder $query, int $ageGroupId): Builder
    {
        return $query->whereHas('ageGroups', fn (Builder $q) => $q->whereKey($ageGroupId));
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
        ];
    }
}
