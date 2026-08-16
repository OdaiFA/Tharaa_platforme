<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountOwnershipTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_account(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/accounts', [
            'name' => 'المحفظة اليومية',
            'type' => 'cash',
            'currency' => 'SAR',
            'initial_balance' => 500,
            'description' => 'مصروفات يومية',
        ]);

        $response->assertRedirect(route('accounts.index'));

        $this->assertDatabaseHas('accounts', [
            'user_id' => $user->id,
            'name' => 'المحفظة اليومية',
            'type' => 'cash',
            'balance' => 500,
        ]);
    }

    public function test_user_only_sees_own_accounts(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        Account::factory()->create(['user_id' => $other->id]);
        $own = Account::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get('/accounts')
            ->assertOk()
            ->assertSee($own->name);
    }

    public function test_user_can_edit_own_account(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get("/accounts/{$account->id}/edit")
            ->assertOk();
    }

    public function test_user_cannot_edit_another_users_account(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $account = Account::factory()->create(['user_id' => $other->id]);

        $this->actingAs($user)
            ->get("/accounts/{$account->id}/edit")
            ->assertForbidden();
    }

    public function test_user_cannot_update_another_users_account(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $account = Account::factory()->create(['user_id' => $other->id]);

        $this->actingAs($user)
            ->put("/accounts/{$account->id}", [
                'name' => 'اختراق',
                'type' => 'cash',
                'currency' => 'SAR',
            ])
            ->assertForbidden();
    }

    public function test_guest_cannot_access_accounts(): void
    {
        $this->get('/accounts')->assertRedirect(route('login'));
    }
}
