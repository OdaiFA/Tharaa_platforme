<?php

namespace App\Livewire\Courses;

use App\Models\Course;
use App\Models\Enrollment;
use App\Services\EnrollmentService;
use Livewire\Component;

class CourseShow extends Component
{
    public Course $course;

    public ?Enrollment $enrollment = null;

    public function mount(Course $course): void
    {
        if (! $course->is_published && ! auth()->user()?->isAdmin()) {
            abort(404);
        }

        $this->course = $course;
        $this->enrollment = auth()->check()
            ? auth()->user()->enrollments()->where('course_id', $course->id)->first()
            : null;
    }

    public function enroll(EnrollmentService $enrollments)
    {
        $this->authorize('enroll', $this->course);

        $enrollments->enroll(auth()->user(), $this->course);

        session()->flash('success', 'تم التسجيل في الدورة بنجاح');

        return redirect()->route('courses.learn', $this->course);
    }

    public function render()
    {
        return view('livewire.courses.course-show', [
            'modules' => $this->course->modules->loadMissing('lessons'),
        ]);
    }
}
