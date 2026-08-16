<?php

namespace App\Services;

use App\Events\UserRegistered;
use App\Models\AgeGroup;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AuthService
{
    /**
     * Register a new user and set up onboarding data (age group, default settings).
     */
    public function register(array $data): User
    {
        $user = DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'financial_level' => $data['financial_level'] ?? 'beginner',
                'currency' => $data['currency'] ?? 'SAR',
            ]);

            $this->assignAgeGroup($user);

            $user->settings()->create([
                'notification_channels' => ['in_app'],
                'language' => 'ar',
                'theme' => 'light',
                'default_currency' => $user->currency,
                'reminder_time' => '20:00:00',
            ]);

            return $user;
        });

        UserRegistered::dispatch($user);

        return $user;
    }

    /**
     * Assign the user's age group from their date of birth (BR-EDU-001, 002).
     */
    public function assignAgeGroup(User $user): void
    {
        $age = $user->age;

        if ($age === null) {
            return;
        }

        $group = AgeGroup::forAge($age);

        if ($group) {
            $user->update(['age_group_id' => $group->id]);
        }
    }
}
