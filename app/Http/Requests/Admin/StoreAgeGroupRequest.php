<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreAgeGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50'],
            'min_age' => ['required', 'integer', 'min:0'],
            'max_age' => ['required', 'integer', 'gte:min_age'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم الفئة العمرية مطلوب',
            'min_age.required' => 'الحد الأدنى مطلوب',
            'max_age.required' => 'الحد الأقصى مطلوب',
            'max_age.gte' => 'الحد الأقصى يجب أن يكون أكبر من أو يساوي الحد الأدنى',
        ];
    }
}
