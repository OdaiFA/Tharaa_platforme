<?php

namespace App\Livewire\Admin\Courses;

use App\Models\Course;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class CoursesIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public bool $published = false;

    public ?int $confirmingDeleteId = null;

    public ?int $confirmingRestoreId = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPublished(): void
    {
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        $this->confirmingRestoreId = null;
        $this->confirmingDeleteId = $id;
    }

    public function cancelDelete(): void
    {
        $this->confirmingDeleteId = null;
    }

    public function delete(int $id): void
    {
        if ($this->confirmingDeleteId !== $id) {
            return;
        }

        Course::withTrashed()->findOrFail($id)->delete();

        $this->confirmingDeleteId = null;
        session()->flash('success', 'تم حذف الدورة بنجاح');
    }

    public function confirmRestore(int $id): void
    {
        $this->confirmingDeleteId = null;
        $this->confirmingRestoreId = $id;
    }

    public function cancelRestore(): void
    {
        $this->confirmingRestoreId = null;
    }

    public function restore(int $id): void
    {
        if ($this->confirmingRestoreId !== $id) {
            return;
        }

        Course::withTrashed()->findOrFail($id)->restore();

        $this->confirmingRestoreId = null;
        session()->flash('success', 'تمت استعادة الدورة بنجاح');
    }

    public function render()
    {
        $courses = Course::query()
            ->withCount(['modules', 'enrollments'])
            ->when($this->search !== '', fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->published, fn ($q) => $q->where('is_published', true))
            ->withTrashed()
            ->latest()
            ->paginate(10);

        return view('livewire.admin.courses.courses-index', [
            'courses' => $courses,
        ]);
    }
}
