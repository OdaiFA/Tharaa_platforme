<?php

namespace Tests\Feature\Livewire\Profile;

use App\Livewire\Profile\ProfileForm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_the_profile_page(): void
    {
        $this->get(route('profile.edit'))->assertRedirect(route('login'));
    }

    public function test_user_can_render_and_update_their_profile(): void
    {
        $user = User::factory()->create(['name' => 'قديم']);

        $this->actingAs($user)
            ->get(route('profile.edit'))
            ->assertOk()
            ->assertSeeLivewire(ProfileForm::class);

        Livewire::actingAs($user)
            ->test(ProfileForm::class)
            ->assertSet('name', 'قديم')
            ->set('name', 'محدث')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('محدث', $user->fresh()->name);
    }

    public function test_required_fields_are_validated(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ProfileForm::class)
            ->set('name', '')
            ->call('save')
            ->assertHasErrors(['name']);
    }

    public function test_user_can_upload_an_avatar(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ProfileForm::class)
            ->set('avatar', UploadedFile::fake()->image('avatar.jpg'))
            ->call('save')
            ->assertHasNoErrors();

        $avatar = $user->fresh()->avatar;
        $this->assertNotNull($avatar);
        Storage::disk('public')->assertExists($avatar);
    }
}
