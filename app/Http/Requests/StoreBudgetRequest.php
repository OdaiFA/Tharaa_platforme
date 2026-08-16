<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBudgetRequest extends FormRequest
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
            'currency' => ['required', 'string', 'size:3'],
            'categories' => ['nullable', 'array'],
            'categories.*.category_id' => ['required', 'exists:categories,id'],
            'categories.*.limit_amount' => ['required', 'numeric', 'min:1'],
            'categories.*.alert_percentage' => ['nullable', 'integer', 'min:1', 'max:100'],
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
            'alert_percentage.required' => 'نسبة التنبيه مطلوبة',
            'alert_percentage.between' => 'نسبة التنبيه يجب أن تكون بين 1 و 100',
            'currency.size' => 'رمز العملة يجب أن يكون 3 أحرف',
            'categories.*.category_id.required' => 'التصنيف مطلوب',
            'categories.*.limit_amount.required' => 'حد التصنيف مطلوب',
            'categories.*.limit_amount.min' => 'حد التصنيف يجب أن يكون أكبر من صفر',
        ];
    }
}
