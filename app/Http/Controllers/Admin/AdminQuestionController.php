<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreQuestionRequest;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminQuestionController extends Controller
{
    public function index(Quiz $quiz): View
    {
        $quiz->load('questions');

        return view('admin.questions.index', compact('quiz'));
    }

    public function store(StoreQuestionRequest $request): RedirectResponse
    {
        QuizQuestion::create($request->validated());

        return back()->with('success', 'تمت إضافة السؤال بنجاح');
    }

    public function update(StoreQuestionRequest $request, QuizQuestion $question): RedirectResponse
    {
        $question->update($request->validated());

        return back()->with('success', 'تم تحديث السؤال بنجاح');
    }

    public function destroy(QuizQuestion $question): RedirectResponse
    {
        $question->delete();

        return back()->with('success', 'تم حذف السؤال بنجاح');
    }
}
