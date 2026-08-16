<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLessonRequest;
use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminLessonController extends Controller
{
    public function index(Module $module): View
    {
        $module->load('lessons');

        return view('admin.lessons.index', compact('module'));
    }

    public function store(StoreLessonRequest $request): RedirectResponse
    {
        Lesson::create($request->validated());

        return back()->with('success', 'تم إنشاء الدرس بنجاح');
    }

    public function update(StoreLessonRequest $request, Lesson $lesson): RedirectResponse
    {
        $lesson->update($request->validated());

        return back()->with('success', 'تم تحديث الدرس بنجاح');
    }

    public function destroy(Lesson $lesson): RedirectResponse
    {
        $lesson->delete();

        return back()->with('success', 'تم حذف الدرس بنجاح');
    }
}
