<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'financial_level' => ['nullable', 'in:beginner,intermediate,advanced'],
            'currency' => ['nullable', 'string', 'size:3'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'الاسم الكامل مطلوب',
            'date_of_birth.before' => 'تاريخ الميلاد يجب أن يكون في الماضي',
            'avatar.image' => 'يجب أن يكون الملف صورة',
            'avatar.mimes' => 'صيغ الصور المسموحة: jpg, jpeg, png',
            'avatar.max' => 'حجم الصورة يجب أن يكون أقل من 2 ميجابايت',
        ];
    }
}
