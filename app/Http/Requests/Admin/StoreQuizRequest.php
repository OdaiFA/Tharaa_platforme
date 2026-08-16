<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuizRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'lesson_id' => ['nullable', 'exists:lessons,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'passing_score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'time_limit_minutes' => ['nullable', 'integer', 'min:1'],
            'max_attempts' => ['nullable', 'integer', 'min:1', 'max:10'],
            'is_published' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'عنوان الاختبار مطلوب',
            'lesson_id.exists' => 'الدرس غير موجود',
            'passing_score.between' => 'درجة النجاح يجب أن تكون بين 0 و 100',
            'max_attempts.between' => 'عدد المحاولات يجب أن يكون بين 1 و 10',
        ];
    }
}
