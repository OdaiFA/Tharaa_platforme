<?php

namespace App\Livewire\Profile;

use App\Services\AuthService;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProfileForm extends Component
{
    use WithFileUploads;

    public string $name = '';

    public ?string $date_of_birth = null;

    public ?string $financial_level = 'beginner';

    public ?string $currency = null;

    public $avatar = null;

    public ?string $existingAvatar = null;

    public ?string $ageGroupName = null;

    public function mount(): void
    {
        $user = auth()->user()->load(['settings', 'ageGroup']);

        $this->name = $user->name;
        $this->date_of_birth = optional($user->date_of_birth)->format('Y-m-d');
        $this->financial_level = $user->financial_level;
        $this->currency = $user->currency;
        $this->existingAvatar = $user->avatar;
        $this->ageGroupName = $user->ageGroup?->name;
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'financial_level' => ['nullable', 'in:beginner,intermediate,advanced'],
            'currency' => ['nullable', 'string', 'size:3'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'الاسم الكامل مطلوب',
            'date_of_birth.before' => 'تاريخ الميلاد يجب أن يكون في الماضي',
            'avatar.image' => 'يجب أن يكون الملف صورة',
            'avatar.mimes' => 'صيغ الصور المسموحة: jpg, jpeg, png',
            'avatar.max' => 'حجم الصورة يجب أن يكون أقل من 2 ميجابايت',
        ];
    }

    public function save(AuthService $authService): void
    {
        $validated = $this->validate();
        unset($validated['avatar']);

        $user = auth()->user();

        if ($this->avatar) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $validated['avatar'] = $this->avatar->store('avatars', 'public');
        }

        $user->update($validated);
        $authService->assignAgeGroup($user);

        $this->existingAvatar = $user->fresh()->avatar;
        $this->avatar = null;
        $this->ageGroupName = $user->fresh()->ageGroup?->name;

        session()->flash('success', 'تم تحديث الملف الشخصي بنجاح');
    }

    public function render()
    {
        return view('livewire.profile.profile-form');
    }
}
