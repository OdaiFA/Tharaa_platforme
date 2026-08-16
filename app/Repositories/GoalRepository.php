<?php

namespace App\Repositories;

use App\Models\Goal;

class GoalRepository extends BaseRepository
{
    protected function model(): string
    {
        return Goal::class;
    }

    public function activeForUser(int $userId)
    {
        return $this->query($userId)
            ->with('contributions')
            ->orderByRaw("FIELD(status, 'active', 'paused', 'completed')")
            ->orderBy('deadline')
            ->get();
    }

    public function completedForUser(int $userId)
    {
        return $this->query($userId)
            ->completed()
            ->count();
    }
}
