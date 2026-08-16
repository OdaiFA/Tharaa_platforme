<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Repositories\CourseRepository;
use App\Services\EnrollmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function __construct(
        private readonly CourseRepository $courses,
        private readonly EnrollmentService $enrollments,
    ) {}

    public function index(): View
    {
        $courses = $this->courses->published([
            'level' => request('level'),
            'age_group_id' => request('age_group_id'),
            'search' => request('search'),
        ]);

        return view('courses.index', compact('courses'));
    }

    public function recommended(): View
    {
        $ageGroupId = auth()->user()?->age_group_id;

        $courses = $this->courses->recommendedFor($ageGroupId);

        return view('courses.recommended', compact('courses'));
    }

    public function show(Course $course): View
    {
        if (! $course->is_published && ! auth()->user()?->isAdmin()) {
            abort(404);
        }

        $enrollment = auth()->check()
            ? auth()->user()->enrollments()->where('course_id', $course->id)->first()
            : null;

        return view('courses.show', compact('course', 'enrollment'));
    }

    public function enroll(Course $course): RedirectResponse
    {
        $this->authorize('enroll', $course);

        $this->enrollments->enroll(auth()->user(), $course);

        return redirect()->route('courses.learn', $course)->with('success', 'تم التسجيل في الدورة بنجاح');
    }

    public function learn(Course $course): View
    {
        $this->authorize('learn', $course);

        $enrollment = auth()->user()->enrollments()->where('course_id', $course->id)->firstOrFail();
        $completedLessons = $enrollment->lessonProgress()
            ->where('status', 'completed')
            ->pluck('lesson_id')
            ->all();

        $course->load(['modules.lessons.quiz']);

        return view('courses.learn', compact('course', 'enrollment', 'completedLessons'));
    }
}
