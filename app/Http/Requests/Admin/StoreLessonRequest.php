<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreLessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'module_id' => ['required', 'exists:modules,id'],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'video_url' => ['nullable', 'url', 'max:255'],
            'duration_minutes' => ['nullable', 'integer', 'min:0'],
            'order_index' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['sometimes', 'boolean'],
            'resources' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'module_id.required' => 'الوحدة مطلوبة',
            'title.required' => 'عنوان الدرس مطلوب',
            'video_url.url' => 'رابط الفيديو غير صالح',
            'duration_minutes.integer' => 'المدة يجب أن تكون رقماً صحيحاً',
        ];
    }
}
