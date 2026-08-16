<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'featured_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'category_id' => ['nullable', 'exists:article_categories,id'],
            'is_published' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'عنوان المقال مطلوب',
            'content.required' => 'محتوى المقال مطلوب',
            'featured_image.image' => 'يجب أن تكون الصورة صورة',
            'featured_image.mimes' => 'صيغ الصور المسموحة: jpg, jpeg, png',
            'featured_image.max' => 'حجم الصورة يجب أن يكون أقل من 2 ميجابايت',
            'category_id.exists' => 'التصنيف غير موجود',
        ];
    }
}
