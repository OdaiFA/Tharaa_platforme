<?php

namespace Tests\Feature\Livewire\Goals;

use App\Livewire\Goals\GoalForm;
use App\Models\Goal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GoalFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_a_goal(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(GoalForm::class)
            ->set('name', 'هدف جديد')
            ->set('target_amount', '1000')
            ->set('deadline', now()->addMonth()->format('Y-m-d'))
            ->set('priority', 'high')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('goals', [
            'user_id' => $user->id,
            'name' => 'هدف جديد',
            'target_amount' => 1000,
        ]);
    }

    public function test_user_can_create_a_goal_with_an_explicit_currency(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(GoalForm::class)
            ->set('name', 'هدف بالدولار')
            ->set('target_amount', '5000')
            ->set('currency_code', 'USD')
            ->set('deadline', now()->addMonth()->format('Y-m-d'))
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('goals', [
            'user_id' => $user->id,
            'name' => 'هدف بالدولار',
            'currency_code' => 'USD',
        ]);
    }

    public function test_goal_defaults_to_the_users_own_currency_when_creating(): void
    {
        $user = User::factory()->create(['currency' => 'EUR']);

        Livewire::actingAs($user)
            ->test(GoalForm::class)
            ->assertSet('currency_code', 'EUR');
    }

    public function test_currency_is_not_editable_after_creation(): void
    {
        $user = User::factory()->create();
        $goal = Goal::factory()->for($user)->create(['currency_code' => 'SAR', 'deadline' => now()->addMonth()]);

        Livewire::actingAs($user)
            ->test(GoalForm::class, ['goalId' => $goal->id])
            ->assertSet('currency_code', 'SAR')
            ->set('name', 'اسم محدث')
            ->call('save')
            ->assertHasNoErrors();

        // currency_code was never part of the update payload — confirm it
        // is untouched regardless of what the (read-only) property holds.
        $this->assertSame('SAR', $goal->fresh()->currency_code);
    }

    public function test_required_fields_are_validated(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(GoalForm::class)
            ->set('name', '')
            ->set('target_amount', '')
            ->call('save')
            ->assertHasErrors(['name', 'target_amount']);
    }

    public function test_deadline_must_not_be_in_the_past_when_creating(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(GoalForm::class)
            ->set('name', 'هدف جديد')
            ->set('target_amount', '1000')
            ->set('deadline', now()->subDay()->format('Y-m-d'))
            ->call('save')
            ->assertHasErrors(['deadline']);
    }

    public function test_user_can_edit_their_own_goal(): void
    {
        $user = User::factory()->create();
        $goal = Goal::factory()->for($user)->create(['name' => 'قديم', 'deadline' => now()->addMonth()]);

        Livewire::actingAs($user)
            ->test(GoalForm::class, ['goalId' => $goal->id])
            ->assertSet('name', 'قديم')
            ->set('name', 'محدث')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('محدث', $goal->fresh()->name);
    }

    public function test_user_cannot_edit_another_users_goal(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $goal = Goal::factory()->for($other)->create();

        Livewire::actingAs($user)
            ->test(GoalForm::class, ['goalId' => $goal->id])
            ->assertForbidden();
    }

    public function test_guest_cannot_access_the_create_form(): void
    {
        $this->get(route('goals.create'))->assertRedirect(route('login'));
    }
}
