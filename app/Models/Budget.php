<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Budget extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'total_amount',
        'start_date',
        'end_date',
        'alert_percentage',
        'currency',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
            'alert_percentage' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function budgetCategories(): HasMany
    {
        return $this->hasMany(BudgetCategory::class);
    }

    public function scopeForUser(Builder $query, ?int $userId = null): Builder
    {
        return $query->where('user_id', $userId ?? auth()->id());
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeInPeriod(Builder $query, $start, $end): Builder
    {
        return $query->where('start_date', '<=', $end)
            ->where('end_date', '>=', $start);
    }
}
