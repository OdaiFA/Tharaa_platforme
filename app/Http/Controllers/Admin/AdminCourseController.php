<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCourseRequest;
use App\Http\Requests\Admin\UpdateCourseRequest;
use App\Models\AgeGroup;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminCourseController extends Controller
{
    public function index(): View
    {
        return view('admin.courses.index');
    }

    public function create(): View
    {
        $ageGroups = AgeGroup::all();

        return view('admin.courses.create', compact('ageGroups'));
    }

    public function store(StoreCourseRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['thumbnail', 'age_groups']);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('courses', 'public');
        }

        $course = Course::create(array_merge($data, [
            'created_by' => auth()->id(),
        ]));

        $course->ageGroups()->sync($request->input('age_groups', []));

        return redirect()->route('admin.courses.index')->with('success', 'تم إنشاء الدورة بنجاح');
    }

    public function edit(Course $course): View
    {
        $ageGroups = AgeGroup::all();
        $course->load('ageGroups', 'modules.lessons.quiz.questions');

        return view('admin.courses.edit', compact('course', 'ageGroups'));
    }

    public function update(UpdateCourseRequest $request, Course $course): RedirectResponse
    {
        $data = $request->safe()->except(['thumbnail', 'age_groups']);

        if ($request->hasFile('thumbnail')) {
            if ($course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail);
            }

            $data['thumbnail'] = $request->file('thumbnail')->store('courses', 'public');
        }

        $course->update($data);
        $course->ageGroups()->sync($request->input('age_groups', []));

        return redirect()->route('admin.courses.index')->with('success', 'تم تحديث الدورة بنجاح');
    }

    public function destroy(Course $course): RedirectResponse
    {
        $course->delete();

        return back()->with('success', 'تم حذف الدورة بنجاح');
    }

    public function restore(int $id): RedirectResponse
    {
        Course::withTrashed()->findOrFail($id)->restore();

        return back()->with('success', 'تمت استعادة الدورة بنجاح');
    }
}
