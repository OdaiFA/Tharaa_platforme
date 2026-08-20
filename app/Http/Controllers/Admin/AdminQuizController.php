<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreQuizRequest;
use App\Models\Lesson;
use App\Models\Quiz;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminQuizController extends Controller
{
    public function index(Lesson $lesson): View
    {
        return view('admin.quizzes.index', compact('lesson'));
    }

    public function store(StoreQuizRequest $request): RedirectResponse
    {
        Quiz::updateOrCreate(
            ['lesson_id' => $request->input('lesson_id')],
            $request->validated(),
        );

        return back()->with('success', 'تم حفظ الاختبار بنجاح');
    }

    public function update(StoreQuizRequest $request, Quiz $quiz): RedirectResponse
    {
        $quiz->update($request->validated());

        return back()->with('success', 'تم تحديث الاختبار بنجاح');
    }

    public function destroy(Quiz $quiz): RedirectResponse
    {
        $quiz->delete();

        return back()->with('success', 'تم حذف الاختبار بنجاح');
    }
}
