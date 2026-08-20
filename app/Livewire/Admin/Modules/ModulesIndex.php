<?php

namespace App\Livewire\Admin\Modules;

use App\Models\Course;
use App\Models\Module;
use Livewire\Component;

class ModulesIndex extends Component
{
    public int $courseId;

    public string $title = '';

    public ?string $description = null;

    public int $order_index = 0;

    public ?int $editingId = null;

    public ?int $confirmingDeleteId = null;

    public function mount(int $courseId): void
    {
        $this->courseId = $courseId;
    }

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'order_index' => ['nullable', 'integer', 'min:0'],
        ];
    }

    protected function messages(): array
    {
        return [
            'title.required' => 'عنوان الوحدة مطلوب',
            'order_index.integer' => 'الترتيب يجب أن يكون رقماً صحيحاً',
        ];
    }

    public function save(): void
    {
        $validated = $this->validate();

        if ($this->editingId) {
            Module::findOrFail($this->editingId)->update($validated);
            session()->flash('success', 'تم تحديث الوحدة بنجاح');
        } else {
            Module::create(array_merge($validated, ['course_id' => $this->courseId]));
            session()->flash('success', 'تم إنشاء الوحدة بنجاح');
        }

        $this->reset(['title', 'description', 'order_index', 'editingId']);
    }

    public function edit(int $id): void
    {
        $module = Module::findOrFail($id);

        $this->editingId = $module->id;
        $this->title = $module->title;
        $this->description = $module->description;
        $this->order_index = $module->order_index;
        $this->resetValidation();
    }

    public function cancelEdit(): void
    {
        $this->reset(['title', 'description', 'order_index', 'editingId']);
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

        Module::findOrFail($id)->delete();
        $this->confirmingDeleteId = null;
        session()->flash('success', 'تم حذف الوحدة بنجاح');
    }

    public function render()
    {
        $course = Course::with(['modules' => fn ($q) => $q->withCount('lessons')->orderBy('order_index')])->findOrFail($this->courseId);

        return view('livewire.admin.modules.modules-index', [
            'course' => $course,
        ]);
    }
}
