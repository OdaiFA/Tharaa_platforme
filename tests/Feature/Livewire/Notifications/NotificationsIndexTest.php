<?php

namespace Tests\Feature\Livewire\Notifications;

use App\Livewire\Notifications\NotificationsIndex;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class NotificationsIndexTest extends TestCase
{
    use RefreshDatabase;

    private function makeNotification(User $user, array $overrides = []): UserNotification
    {
        return UserNotification::create(array_merge([
            'type' => 'test',
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'title' => 'عنوان',
            'message' => 'رسالة تجريبية',
            'is_read' => false,
        ], $overrides));
    }

    public function test_guest_cannot_access_notifications(): void
    {
        $this->get(route('notifications.index'))->assertRedirect(route('login'));
    }

    public function test_user_can_view_their_notifications(): void
    {
        $user = User::factory()->create();
        $this->makeNotification($user);

        $this->actingAs($user)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSeeLivewire(NotificationsIndex::class);
    }

    public function test_user_can_mark_a_notification_as_read(): void
    {
        $user = User::factory()->create();
        $notification = $this->makeNotification($user);

        Livewire::actingAs($user)
            ->test(NotificationsIndex::class)
            ->call('markAsRead', $notification->id);

        $this->assertTrue($notification->fresh()->is_read);
    }

    public function test_user_can_mark_all_notifications_as_read(): void
    {
        $user = User::factory()->create();
        $this->makeNotification($user);
        $this->makeNotification($user);

        Livewire::actingAs($user)
            ->test(NotificationsIndex::class)
            ->call('markAllAsRead');

        $this->assertSame(0, UserNotification::where('notifiable_id', $user->id)->where('is_read', false)->count());
    }

    public function test_user_cannot_mark_another_users_notification_as_read(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $notification = $this->makeNotification($other);

        Livewire::actingAs($user)
            ->test(NotificationsIndex::class)
            ->call('markAsRead', $notification->id)
            ->assertForbidden();

        $this->assertFalse($notification->fresh()->is_read);
    }
}
