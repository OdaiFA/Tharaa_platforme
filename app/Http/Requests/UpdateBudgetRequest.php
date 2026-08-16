<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBudgetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'total_amount' => ['required', 'numeric', 'min:1'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'alert_percentage' => ['required', 'integer', 'min:1', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم الميزانية مطلوب',
            'total_amount.required' => 'إجمالي الميزانية مطلوب',
            'total_amount.min' => 'إجمالي الميزانية يجب أن يكون أكبر من صفر',
            'start_date.required' => 'تاريخ البداية مطلوب',
            'end_date.required' => 'تاريخ النهاية مطلوب',
            'end_date.after_or_equal' => 'تاريخ النهاية يجب أن يكون بعد أو يساوي تاريخ البداية',
            'alert_percentage.between' => 'نسبة التنبيه يجب أن تكون بين 1 و 100',
        ];
    }
}
