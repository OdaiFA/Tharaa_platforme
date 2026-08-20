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
            // Nullable, not required: omitting it must not break existing
            // API clients that predate goal currency. The controller falls
            // back to the user's own default currency when absent.
            'currency_code' => ['nullable', 'string', 'size:3'],
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
            'currency_code.size' => 'رمز العملة يجب أن يكون 3 أحرف',
            'deadline.required' => 'تاريخ الانتهاء مطلوب',
            'deadline.after_or_equal' => 'تاريخ الانتهاء يجب أن يكون اليوم أو في المستقبل',
            'priority.required' => 'الأولوية مطلوبة',
            'priority.in' => 'الأولوية غير صالحة',
        ];
    }
}
