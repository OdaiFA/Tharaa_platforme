<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'quiz_id' => ['required', 'exists:quizzes,id'],
            'question' => ['required', 'string'],
            'type' => ['required', Rule::in(['multiple_choice', 'true_false'])],
            'options' => ['required', 'array', 'min:2'],
            'options.*' => ['required', 'string'],
            'correct_answer' => ['required', 'array', 'min:1'],
            'points' => ['nullable', 'integer', 'min:1'],
            'order_index' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'quiz_id.required' => 'الاختبار مطلوب',
            'question.required' => 'نص السؤال مطلوب',
            'type.required' => 'نوع السؤال مطلوب',
            'type.in' => 'نوع السؤال غير صالح',
            'options.required' => 'الخيارات مطلوبة',
            'options.min' => 'يجب توفير خيارين على الأقل',
            'correct_answer.required' => 'الإجابة الصحيحة مطلوبة',
            'points.integer' => 'النقاط يجب أن تكون رقماً صحيحاً',
        ];
    }
}
