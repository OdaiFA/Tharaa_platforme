<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AgeGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'min_age',
        'max_age',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_age_group');
    }

    public static function forAge(int $age): ?self
    {
        return static::query()
            ->where('min_age', '<=', $age)
            ->where('max_age', '>=', $age)
            ->first();
    }
}
