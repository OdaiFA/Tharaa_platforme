<?php

namespace App\Livewire\Learning;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Services\EnrollmentService;
use Livewire\Component;

class CourseLearn extends Component
{
    public Course $course;

    public Enrollment $enrollment;

    public array $completedLessons = [];

    public function mount(Course $course): void
    {
        $this->authorize('learn', $course);

        $this->course = $course;
        $this->enrollment = auth()->user()->enrollments()->where('course_id', $course->id)->firstOrFail();
        $this->course->load(['modules.lessons.quiz']);
        $this->refreshCompletedLessons();
    }

    protected function refreshCompletedLessons(): void
    {
        $this->completedLessons = $this->enrollment->lessonProgress()
            ->where('status', 'completed')
            ->pluck('lesson_id')
            ->all();
    }

    public function completeLesson(int $lessonId, EnrollmentService $enrollments): void
    {
        $lesson = Lesson::findOrFail($lessonId);
        $this->authorize('complete', $lesson);

        $enrollments->completeLesson(auth()->user(), $lesson);

        $this->enrollment->refresh();
        $this->refreshCompletedLessons();

        session()->flash('success', 'تم إكمال الدرس بنجاح');
    }

    public function render()
    {
        return view('livewire.learning.course-learn');
    }
}
