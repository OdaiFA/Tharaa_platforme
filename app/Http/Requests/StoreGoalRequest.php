<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGoalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'target_amount' => ['required', 'numeric', 'min:0.01'],
            'deadline' => ['required', 'date', 'after_or_equal:today'],
            'priority' => ['required', Rule::in(['low', 'medium', 'high'])],
            'icon' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم الهدف مطلوب',
            'target_amount.required' => 'المبلغ المستهدف مطلوب',
            'target_amount.min' => 'المبلغ المستهدف يجب أن يكون أكبر من صفر',
            'deadline.required' => 'تاريخ الانتهاء مطلوب',
            'deadline.after_or_equal' => 'تاريخ الانتهاء يجب أن يكون اليوم أو في المستقبل',
            'priority.required' => 'الأولوية مطلوبة',
            'priority.in' => 'الأولوية غير صالحة',
        ];
    }
}
