<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'account_id' => ['required', 'exists:accounts,id'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'type' => ['required', Rule::in(['income', 'expense', 'transfer'])],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string', 'max:1000'],
            'transaction_date' => ['required', 'date'],
            'is_recurring' => ['sometimes', 'boolean'],
            'recurrence_type' => ['nullable', 'required_if:is_recurring,true', Rule::in(['daily', 'weekly', 'monthly', 'yearly'])],
            'recurrence_end_date' => ['nullable', 'date', 'after_or_equal:transaction_date'],
            'transfer_to_account_id' => ['nullable', 'required_if:type,transfer', 'exists:accounts,id', 'different:account_id'],
        ];
    }

    public function messages(): array
    {
        return [
            'account_id.required' => 'الحساب مطلوب',
            'account_id.exists' => 'الحساب غير موجود',
            'category_id.exists' => 'التصنيف غير موجود',
            'type.required' => 'نوع المعاملة مطلوب',
            'type.in' => 'نوع المعاملة غير صالح',
            'amount.required' => 'المبلغ مطلوب',
            'amount.numeric' => 'المبلغ يجب أن يكون رقماً',
            'amount.min' => 'المبلغ يجب أن يكون أكبر من صفر',
            'transaction_date.required' => 'تاريخ المعاملة مطلوب',
            'recurrence_type.required_if' => 'نوع التكرار مطلوب للمعاملات المتكررة',
            'transfer_to_account_id.different' => 'لا يمكن التحويل إلى نفس الحساب',
        ];
    }
}
