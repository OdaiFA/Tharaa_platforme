<?php

namespace App\Livewire\Admin\Lessons;

use App\Models\Lesson;
use App\Models\Module;
use Livewire\Component;

class LessonsIndex extends Component
{
    public int $moduleId;

    public string $title = '';

    public ?string $content = null;

    public ?string $video_url = null;

    public int $duration_minutes = 0;

    public int $order_index = 0;

    public ?int $editingId = null;

    public ?int $confirmingDeleteId = null;

    public function mount(int $moduleId): void
    {
        $this->moduleId = $moduleId;
    }

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'video_url' => ['nullable', 'url', 'max:255'],
            'duration_minutes' => ['nullable', 'integer', 'min:0'],
            'order_index' => ['nullable', 'integer', 'min:0'],
        ];
    }

    protected function messages(): array
    {
        return [
            'title.required' => 'عنوان الدرس مطلوب',
            'video_url.url' => 'رابط الفيديو غير صالح',
            'duration_minutes.integer' => 'المدة يجب أن تكون رقماً صحيحاً',
        ];
    }

    public function save(): void
    {
        $validated = $this->validate();

        if ($this->editingId) {
            Lesson::findOrFail($this->editingId)->update($validated);
            session()->flash('success', 'تم تحديث الدرس بنجاح');
        } else {
            Lesson::create(array_merge($validated, ['module_id' => $this->moduleId]));
            session()->flash('success', 'تم إنشاء الدرس بنجاح');
        }

        $this->reset(['title', 'content', 'video_url', 'duration_minutes', 'order_index', 'editingId']);
    }

    public function edit(int $id): void
    {
        $lesson = Lesson::findOrFail($id);

        $this->editingId = $lesson->id;
        $this->title = $lesson->title;
        $this->content = $lesson->content;
        $this->video_url = $lesson->video_url;
        $this->duration_minutes = $lesson->duration_minutes;
        $this->order_index = $lesson->order_index;
        $this->resetValidation();
    }

    public function cancelEdit(): void
    {
        $this->reset(['title', 'content', 'video_url', 'duration_minutes', 'order_index', 'editingId']);
        $this->resetValidation();
    }

    public function confirmDelete(int $id): void
    {
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

        Lesson::findOrFail($id)->delete();
        $this->confirmingDeleteId = null;
        session()->flash('success', 'تم حذف الدرس بنجاح');
    }

    public function render()
    {
        $module = Module::findOrFail($this->moduleId);
        $module->setRelation('lessons', $module->lessons()->orderBy('order_index')->get());

        return view('livewire.admin.lessons.lessons-index', [
            'module' => $module,
        ]);
    }
}
