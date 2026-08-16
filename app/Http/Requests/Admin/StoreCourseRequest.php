<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'level' => ['required', Rule::in(['beginner', 'intermediate', 'advanced'])],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'duration_hours' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['sometimes', 'boolean'],
            'certificate_enabled' => ['sometimes', 'boolean'],
            'passing_score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'age_groups' => ['nullable', 'array'],
            'age_groups.*' => ['exists:age_groups,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'عنوان الدورة مطلوب',
            'level.required' => 'مستوى الدورة مطلوب',
            'level.in' => 'مستوى الدورة غير صالح',
            'thumbnail.image' => 'يجب أن تكون الصورة صورة',
            'thumbnail.mimes' => 'صيغ الصور المسموحة: jpg, jpeg, png',
            'thumbnail.max' => 'حجم الصورة يجب أن يكون أقل من 2 ميجابايت',
            'passing_score.between' => 'درجة النجاح يجب أن تكون بين 0 و 100',
            'age_groups.*' => 'الفئة العمرية غير صالحة',
        ];
    }
}
