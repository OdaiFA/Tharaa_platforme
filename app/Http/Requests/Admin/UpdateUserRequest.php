<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'unique:users,email,' . $this->route('user')],
            'role' => ['sometimes', Rule::in(['user', 'admin'])],
            'financial_level' => ['sometimes', Rule::in(['beginner', 'intermediate', 'advanced'])],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.max' => 'الاسم يجب ألا يتجاوز 255 حرفاً',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل',
            'role.in' => 'الدور غير صالح',
            'financial_level.in' => 'المستوى المالي غير صالح',
        ];
    }
}
