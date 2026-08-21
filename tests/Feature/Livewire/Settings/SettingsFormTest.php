<?php

namespace Tests\Feature\Livewire\Settings;

use App\Livewire\Settings\SettingsForm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SettingsFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_settings(): void
    {
        $this->get(route('settings.index'))->assertRedirect(route('login'));
    }

    public function test_user_can_render_and_update_settings_creating_them_if_missing(): void
    {
        $user = User::factory()->create();
        $this->assertFalse($user->settings()->exists());

        $this->actingAs($user)
            ->get(route('settings.index'))
            ->assertOk()
            ->assertSeeLivewire(SettingsForm::class);

        Livewire::actingAs($user)
            ->test(SettingsForm::class)
            ->set('theme', 'dark')
            ->set('language', 'en')
            ->set('notification_channels', ['in_app', 'email'])
            ->call('save')
            ->assertHasNoErrors();

        $settings = $user->settings()->first();
        $this->assertSame('dark', $settings->theme);
        $this->assertSame('en', $settings->language);
        $this->assertSame(['in_app', 'email'], $settings->notification_channels);
    }

    public function test_user_can_update_existing_settings(): void
    {
        $user = User::factory()->create();
        $user->settings()->create(['theme' => 'light', 'language' => 'ar']);

        Livewire::actingAs($user)
            ->test(SettingsForm::class)
            ->assertSet('theme', 'light')
            ->set('theme', 'dark')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('dark', $user->settings()->first()->theme);
        $this->assertSame(1, $user->settings()->count());
    }

    public function test_invalid_theme_is_rejected(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(SettingsForm::class)
            ->set('theme', 'neon')
            ->call('save')
            ->assertHasErrors(['theme']);
    }
}
