<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContributeGoalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:0.01'],
            'account_id' => ['nullable', 'exists:accounts,id'],
            'note' => ['nullable', 'string', 'max:1000'],
            'contribution_date' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'مبلغ المساهمة مطلوب',
            'amount.numeric' => 'مبلغ المساهمة يجب أن يكون رقماً',
            'amount.min' => 'مبلغ المساهمة يجب أن يكون أكبر من صفر',
            'account_id.exists' => 'الحساب غير موجود',
        ];
    }
}
