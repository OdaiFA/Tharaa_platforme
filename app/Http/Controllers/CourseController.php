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
        return view('courses.index');
    }

    public function recommended(): View
    {
        return view('courses.recommended');
    }

    public function show(Course $course): View
    {
        if (! $course->is_published && ! auth()->user()?->isAdmin()) {
            abort(404);
        }

        return view('courses.show', compact('course'));
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

        return view('courses.learn', compact('course'));
    }
}
