<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserSetting;

class UserSettingPolicy
{
    public function view(User $user, UserSetting $setting): bool
    {
        return $user->isAdmin() || $setting->user_id === $user->id;
    }

    public function update(User $user, UserSetting $setting): bool
    {
        return $setting->user_id === $user->id;
    }
}
