<?php

namespace Tests\Feature\Livewire\Accounts;

use App\Livewire\Accounts\AccountsIndex;
use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AccountsIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_render_accounts_index(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('accounts.index'))
            ->assertOk()
            ->assertSeeLivewire(AccountsIndex::class);
    }

    public function test_guest_cannot_access_accounts_index(): void
    {
        $this->get(route('accounts.index'))->assertRedirect(route('login'));
    }

    public function test_user_only_sees_own_accounts(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        Account::factory()->for($user)->create(['name' => 'حسابي']);
        Account::factory()->for($other)->create(['name' => 'حساب آخر']);

        Livewire::actingAs($user)
            ->test(AccountsIndex::class)
            ->assertSee('حسابي')
            ->assertDontSee('حساب آخر');
    }

    public function test_delete_requires_confirmation_then_removes_the_account(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->for($user)->create();

        Livewire::actingAs($user)
            ->test(AccountsIndex::class)
            ->call('delete', $account->id);

        $this->assertModelExists($account);

        Livewire::actingAs($user)
            ->test(AccountsIndex::class)
            ->call('confirmDelete', $account->id)
            ->call('delete', $account->id);

        $this->assertSoftDeleted($account);
    }

    public function test_user_cannot_delete_another_users_account(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $account = Account::factory()->for($other)->create();

        Livewire::actingAs($user)
            ->test(AccountsIndex::class)
            ->call('confirmDelete', $account->id)
            ->call('delete', $account->id)
            ->assertNotFound();

        $this->assertModelExists($account);
    }
}
