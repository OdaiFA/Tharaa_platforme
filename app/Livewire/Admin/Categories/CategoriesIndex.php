<?php

namespace App\Livewire\Admin\Categories;

use App\Models\Category;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Component;

class CategoriesIndex extends Component
{
    #[Url]
    public string $type = 'expense';

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('nullable|string|max:255')]
    public ?string $icon = null;

    #[Validate('nullable|string|regex:/^#[0-9A-Fa-f]{6}$/')]
    public ?string $color = null;

    public ?int $editingId = null;

    public ?int $confirmingDeleteId = null;

    public function mount(): void
    {
        if (! in_array($this->type, ['income', 'expense'], true)) {
            $this->type = 'expense';
        }
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'اسم التصنيف مطلوب',
            'color.regex' => 'صيغة اللون غير صحيحة (مثال: #FF0000)',
        ];
    }

    public function setType(string $type): void
    {
        if (! in_array($type, ['income', 'expense'], true)) {
            return;
        }

        $this->type = $type;
        $this->cancelEdit();
        $this->cancelDelete();
    }

    public function save(): void
    {
        $validated = $this->validate();
        $validated['type'] = $this->type;

        if ($this->editingId) {
            Category::findOrFail($this->editingId)->update($validated);
            session()->flash('success', 'تم تحديث التصنيف بنجاح');
        } else {
            $validated['is_system'] = false;
            Category::create($validated);
            session()->flash('success', 'تم إنشاء التصنيف بنجاح');
        }

        $this->reset(['name', 'icon', 'color', 'editingId']);
    }

    public function edit(int $id): void
    {
        $category = Category::findOrFail($id);

        $this->editingId = $category->id;
        $this->name = $category->name;
        $this->icon = $category->icon;
        $this->color = $category->color;
        $this->confirmingDeleteId = null;
        $this->resetValidation();
    }

    public function cancelEdit(): void
    {
        $this->reset(['name', 'icon', 'color', 'editingId']);
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

        $category = Category::findOrFail($id);

        if ($category->is_system) {
            $this->confirmingDeleteId = null;
            session()->flash('error', 'لا يمكن حذف التصنيفات النظامية');

            return;
        }

        if ($this->editingId === $category->id) {
            $this->cancelEdit();
        }

        $category->delete();
        $this->confirmingDeleteId = null;
        session()->flash('success', 'تم حذف التصنيف بنجاح');
    }

    public function render()
    {
        $categories = Category::query()
            ->where('type', $this->type)
            ->orderBy('name')
            ->get();

        return view('livewire.admin.categories.categories-index', [
            'categories' => $categories,
        ]);
    }
}
