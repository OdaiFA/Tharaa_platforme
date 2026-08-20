<?php

namespace Tests\Feature\Livewire\Goals;

use App\Livewire\Goals\GoalsIndex;
use App\Models\Account;
use App\Models\Goal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GoalsIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_render_goals_index(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('goals.index'))
            ->assertOk()
            ->assertSeeLivewire(GoalsIndex::class);
    }

    public function test_guest_cannot_access_goals_index(): void
    {
        $this->get(route('goals.index'))->assertRedirect(route('login'));
    }

    public function test_user_only_sees_own_goals(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        Goal::factory()->for($user)->create(['name' => 'هدفي']);
        Goal::factory()->for($other)->create(['name' => 'هدف آخر']);

        Livewire::actingAs($user)
            ->test(GoalsIndex::class)
            ->assertSee('هدفي')
            ->assertDontSee('هدف آخر');
    }

    public function test_user_can_contribute_to_their_goal(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->for($user)->create(['balance' => 1000]);
        $goal = Goal::factory()->for($user)->create(['target_amount' => 500, 'current_amount' => 0]);

        Livewire::actingAs($user)
            ->test(GoalsIndex::class)
            ->call('startContribute', $goal->id)
            ->set('amount', '200')
            ->set('account_id', (string) $account->id)
            ->call('contribute')
            ->assertHasNoErrors();

        $this->assertEquals(200, $goal->fresh()->current_amount);
    }

    public function test_contribution_exceeding_account_balance_shows_error(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->for($user)->create(['balance' => 50]);
        $goal = Goal::factory()->for($user)->create(['target_amount' => 500, 'current_amount' => 0]);

        Livewire::actingAs($user)
            ->test(GoalsIndex::class)
            ->call('startContribute', $goal->id)
            ->set('amount', '200')
            ->set('account_id', (string) $account->id)
            ->call('contribute')
            ->assertHasErrors(['amount']);

        $this->assertEquals(0, $goal->fresh()->current_amount);
    }

    public function test_cross_currency_contribution_is_rejected_with_a_clear_error(): void
    {
        $user = User::factory()->create();
        $usdAccount = Account::factory()->for($user)->create(['currency' => 'USD', 'balance' => 1000]);
        $goal = Goal::factory()->for($user)->create(['currency_code' => 'SAR', 'target_amount' => 500, 'current_amount' => 0]);

        Livewire::actingAs($user)
            ->test(GoalsIndex::class)
            ->call('startContribute', $goal->id)
            ->set('amount', '200')
            ->set('account_id', (string) $usdAccount->id)
            ->call('contribute')
            ->assertHasErrors(['account_id']);

        $this->assertEquals(0, $goal->fresh()->current_amount);
    }

    public function test_same_currency_contribution_succeeds(): void
    {
        $user = User::factory()->create();
        $sarAccount = Account::factory()->for($user)->create(['currency' => 'SAR', 'balance' => 1000]);
        $goal = Goal::factory()->for($user)->create(['currency_code' => 'SAR', 'target_amount' => 500, 'current_amount' => 0]);

        Livewire::actingAs($user)
            ->test(GoalsIndex::class)
            ->call('startContribute', $goal->id)
            ->set('amount', '200')
            ->set('account_id', (string) $sarAccount->id)
            ->call('contribute')
            ->assertHasNoErrors();

        $this->assertEquals(200, $goal->fresh()->current_amount);
    }

    public function test_delete_requires_confirmation_and_preserves_contributions(): void
    {
        $user = User::factory()->create();
        $goal = Goal::factory()->for($user)->create();

        Livewire::actingAs($user)
            ->test(GoalsIndex::class)
            ->call('delete', $goal->id);

        $this->assertModelExists($goal);

        Livewire::actingAs($user)
            ->test(GoalsIndex::class)
            ->call('confirmDelete', $goal->id)
            ->call('delete', $goal->id);

        $this->assertSoftDeleted($goal);
    }

    public function test_user_cannot_delete_another_users_goal(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $goal = Goal::factory()->for($other)->create();

        Livewire::actingAs($user)
            ->test(GoalsIndex::class)
            ->call('confirmDelete', $goal->id)
            ->call('delete', $goal->id)
            ->assertNotFound();

        $this->assertModelExists($goal);
    }
}
