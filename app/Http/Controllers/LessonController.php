<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Services\EnrollmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function __construct(private readonly EnrollmentService $enrollments) {}

    public function complete(Request $request, Lesson $lesson): RedirectResponse
    {
        $this->authorize('complete', $lesson);

        $this->enrollments->completeLesson(auth()->user(), $lesson);

        return back()->with('success', 'تم إكمال الدرس بنجاح');
    }
}
