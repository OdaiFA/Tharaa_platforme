<?php

namespace App\Repositories;

use App\Models\Budget;
use App\Models\Category;

class BudgetRepository extends BaseRepository
{
    protected function model(): string
    {
        return Budget::class;
    }

    public function activeForUser(int $userId)
    {
        return $this->query($userId)
            ->active()
            ->with('budgetCategories.category')
            ->latest()
            ->get();
    }

    public function expenseCategories(): array
    {
        return Category::query()->expense()->orderBy('name')->get()->all();
    }
}
