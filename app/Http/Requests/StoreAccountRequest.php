<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['cash', 'bank', 'savings', 'electronic'])],
            'currency' => ['required', 'string', 'size:3'],
            'initial_balance' => ['required', 'numeric', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
            'description' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم الحساب مطلوب',
            'type.required' => 'نوع الحساب مطلوب',
            'type.in' => 'نوع الحساب غير صالح',
            'currency.required' => 'العملة مطلوبة',
            'currency.size' => 'رمز العملة يجب أن يكون 3 أحرف',
            'initial_balance.required' => 'الرصيد الابتدائي مطلوب',
            'initial_balance.min' => 'الرصيد الابتدائي يجب أن يكون أكبر أو يساوي صفر',
        ];
    }
}
