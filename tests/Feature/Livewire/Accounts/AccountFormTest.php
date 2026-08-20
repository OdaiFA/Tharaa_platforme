<?php

namespace Tests\Feature\Livewire\Accounts;

use App\Livewire\Accounts\AccountForm;
use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AccountFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_an_account_with_initial_balance(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(AccountForm::class)
            ->set('name', 'حسابي الجديد')
            ->set('type', 'bank')
            ->set('currency', 'SAR')
            ->set('initial_balance', '500')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('accounts', [
            'user_id' => $user->id,
            'name' => 'حسابي الجديد',
            'balance' => 500,
        ]);
    }

    public function test_required_fields_are_validated(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(AccountForm::class)
            ->set('name', '')
            ->call('save')
            ->assertHasErrors(['name' => 'required']);

        $this->assertDatabaseCount('accounts', 0);
    }

    public function test_user_can_edit_their_own_account(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->for($user)->create(['name' => 'قديم']);

        Livewire::actingAs($user)
            ->test(AccountForm::class, ['accountId' => $account->id])
            ->assertSet('name', 'قديم')
            ->set('name', 'محدث')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('محدث', $account->fresh()->name);
    }

    public function test_editing_does_not_expose_or_change_balance_via_initial_balance_field(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->for($user)->create(['balance' => 250, 'initial_balance' => 250]);

        Livewire::actingAs($user)
            ->test(AccountForm::class, ['accountId' => $account->id])
            ->set('name', 'محدث')
            ->call('save');

        $this->assertEquals(250, $account->fresh()->balance);
    }

    public function test_user_cannot_edit_another_users_account(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $account = Account::factory()->for($other)->create();

        Livewire::actingAs($user)
            ->test(AccountForm::class, ['accountId' => $account->id])
            ->assertForbidden();
    }

    public function test_guest_cannot_access_the_create_form(): void
    {
        $this->get(route('accounts.create'))->assertRedirect(route('login'));
    }
}
