<?php

namespace Tests\Feature\Livewire\Admin;

use App\Livewire\Admin\AgeGroups\AgeGroupsIndex;
use App\Models\AgeGroup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AgeGroupsIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_render_the_age_groups_page(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.age-groups.index'))
            ->assertOk()
            ->assertSeeLivewire(AgeGroupsIndex::class);
    }

    public function test_regular_user_cannot_access_the_age_groups_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.age-groups.index'))
            ->assertForbidden();
    }

    public function test_admin_can_create_an_age_group(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(AgeGroupsIndex::class)
            ->set('name', 'أطفال 7-12')
            ->set('min_age', 7)
            ->set('max_age', 12)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('age_groups', [
            'name' => 'أطفال 7-12',
            'min_age' => 7,
            'max_age' => 12,
        ]);
    }

    public function test_validation_rejects_invalid_data(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(AgeGroupsIndex::class)
            ->set('name', '')
            ->set('min_age', 10)
            ->set('max_age', 5)
            ->call('save')
            ->assertHasErrors(['name' => 'required', 'max_age' => 'gte']);

        $this->assertDatabaseCount('age_groups', 0);
    }

    public function test_admin_can_edit_an_age_group(): void
    {
        $admin = User::factory()->admin()->create();
        $ageGroup = AgeGroup::factory()->create([
            'name' => 'شباب',
            'min_age' => 13,
            'max_age' => 17,
        ]);

        Livewire::actingAs($admin)
            ->test(AgeGroupsIndex::class)
            ->call('edit', $ageGroup->id)
            ->assertSet('name', 'شباب')
            ->assertSet('min_age', 13)
            ->assertSet('max_age', 17)
            ->set('name', 'مراهقون')
            ->set('max_age', 18)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('age_groups', [
            'id' => $ageGroup->id,
            'name' => 'مراهقون',
            'min_age' => 13,
            'max_age' => 18,
        ]);
    }

    public function test_existing_age_group_business_constraints_remain_enforced(): void
    {
        $admin = User::factory()->admin()->create();
        AgeGroup::factory()->create(['min_age' => 0, 'max_age' => 6]);

        Livewire::actingAs($admin)
            ->test(AgeGroupsIndex::class)
            ->set('name', 'فئة غير صالحة')
            ->set('min_age', 20)
            ->set('max_age', 10)
            ->call('save')
            ->assertHasErrors(['max_age' => 'gte']);

        $this->assertDatabaseCount('age_groups', 1);
    }
}
