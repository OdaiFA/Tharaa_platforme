<?php

namespace App\Livewire\Admin\Courses;

use App\Models\AgeGroup;
use App\Models\Course;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class CourseForm extends Component
{
    use WithFileUploads;

    public ?int $courseId = null;

    public string $title = '';

    public ?string $description = null;

    public string $level = 'beginner';

    public $thumbnail = null;

    public ?string $existingThumbnail = null;

    public ?int $duration_hours = 2;

    public bool $is_published = false;

    public bool $certificate_enabled = true;

    public ?int $passing_score = 60;

    public array $age_groups = [];

    public function mount(?int $courseId = null): void
    {
        if ($courseId) {
            $course = Course::findOrFail($courseId);

            $this->courseId = $course->id;
            $this->title = $course->title;
            $this->description = $course->description;
            $this->level = $course->level;
            $this->existingThumbnail = $course->thumbnail;
            $this->duration_hours = $course->duration_hours;
            $this->is_published = $course->is_published;
            $this->certificate_enabled = $course->certificate_enabled;
            $this->passing_score = $course->passing_score;
            $this->age_groups = $course->ageGroups()->pluck('age_groups.id')->all();
        }
    }

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'level' => ['required', Rule::in(['beginner', 'intermediate', 'advanced'])],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'duration_hours' => ['nullable', 'integer', 'min:0'],
            'passing_score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'age_groups' => ['nullable', 'array'],
            'age_groups.*' => ['exists:age_groups,id'],
        ];
    }

    protected function messages(): array
    {
        return [
            'title.required' => 'عنوان الدورة مطلوب',
            'level.required' => 'مستوى الدورة مطلوب',
            'level.in' => 'مستوى الدورة غير صالح',
            'thumbnail.image' => 'يجب أن تكون الصورة صورة',
            'thumbnail.mimes' => 'صيغ الصور المسموحة: jpg, jpeg, png',
            'thumbnail.max' => 'حجم الصورة يجب أن يكون أقل من 2 ميجابايت',
            'age_groups.*' => 'الفئة العمرية غير صالحة',
        ];
    }

    public function save(): mixed
    {
        $validated = $this->validate();
        unset($validated['thumbnail'], $validated['age_groups']);

        if ($this->courseId) {
            $course = Course::findOrFail($this->courseId);

            if ($this->thumbnail) {
                if ($course->thumbnail) {
                    Storage::disk('public')->delete($course->thumbnail);
                }
                $validated['thumbnail'] = $this->thumbnail->store('courses', 'public');
            }

            $course->update(array_merge($validated, [
                'is_published' => $this->is_published,
                'certificate_enabled' => $this->certificate_enabled,
            ]));
            $course->ageGroups()->sync($this->age_groups);

            session()->flash('success', 'تم تحديث الدورة بنجاح');
        } else {
            if ($this->thumbnail) {
                $validated['thumbnail'] = $this->thumbnail->store('courses', 'public');
            }

            $course = Course::create(array_merge($validated, [
                'is_published' => $this->is_published,
                'certificate_enabled' => $this->certificate_enabled,
                'created_by' => auth()->id(),
            ]));
            $course->ageGroups()->sync($this->age_groups);

            session()->flash('success', 'تم إنشاء الدورة بنجاح');
        }

        return redirect()->route('admin.courses.index');
    }

    public function render()
    {
        return view('livewire.admin.courses.course-form', [
            'ageGroups' => AgeGroup::orderBy('min_age')->get(),
        ]);
    }
}
