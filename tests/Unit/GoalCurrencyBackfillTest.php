<?php

namespace Tests\Unit;

use App\Models\Goal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Regression test for the backfill logic in
 * database/migrations/2026_08_20_000001_add_currency_code_to_goals_table.php
 * (Strategy B — infer from the goal owner's users.currency).
 *
 * The migration itself already ran against the real dev database and was
 * verified directly (see docs/financial-hardening/GOAL_CURRENCY_ARCHITECTURE.md,
 * "Data Migration" — all 40 pre-existing goals backfilled to their owner's
 * currency with zero nulls). Re-running DDL mid-test is unsafe here (MySQL
 * DDL is not transactional, so it would not be rolled back by
 * RefreshDatabase and could corrupt the schema for later tests). Instead,
 * this test exercises the exact same backfill UPDATE statement the
 * migration uses, against rows deliberately seeded as "not yet backfilled",
 * which is safe (pure DML, fully transactional) and verifies the same logic.
 */
class GoalCurrencyBackfillTest extends TestCase
{
    use RefreshDatabase;

    public function test_backfill_sets_each_goals_currency_from_its_owners_currency(): void
    {
        $sarUser = User::factory()->create(['currency' => 'SAR']);
        $usdUser = User::factory()->create(['currency' => 'USD']);

        $sarGoal = Goal::factory()->for($sarUser)->create(['currency_code' => 'SAR']);
        $usdGoal = Goal::factory()->for($usdUser)->create(['currency_code' => 'SAR']); // deliberately wrong, simulating "not yet backfilled"

        // The exact backfill statement from the migration's up() method.
        DB::table('goals')
            ->join('users', 'users.id', '=', 'goals.user_id')
            ->update(['goals.currency_code' => DB::raw('users.currency')]);

        $this->assertSame('SAR', $sarGoal->fresh()->currency_code);
        $this->assertSame('USD', $usdGoal->fresh()->currency_code);
    }

    public function test_no_goal_is_left_with_a_null_currency_after_backfill(): void
    {
        $users = User::factory()->count(3)->create();
        $users->each(fn (User $u) => Goal::factory()->for($u)->create());

        DB::table('goals')
            ->join('users', 'users.id', '=', 'goals.user_id')
            ->update(['goals.currency_code' => DB::raw('users.currency')]);

        $this->assertSame(0, Goal::whereNull('currency_code')->count());
    }
}
