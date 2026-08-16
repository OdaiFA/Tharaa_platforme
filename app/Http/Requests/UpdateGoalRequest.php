<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGoalRequest extends FormRequest
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
            'deadline' => ['required', 'date'],
            'priority' => ['required', Rule::in(['low', 'medium', 'high'])],
            'status' => ['sometimes', Rule::in(['active', 'paused', 'completed'])],
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
            'priority.required' => 'الأولوية مطلوبة',
            'priority.in' => 'الأولوية غير صالحة',
            'status.in' => 'حالة الهدف غير صالحة',
        ];
    }
}
