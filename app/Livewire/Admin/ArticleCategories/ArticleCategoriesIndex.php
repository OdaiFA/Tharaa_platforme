<?php

namespace App\Livewire\Admin\ArticleCategories;

use App\Models\ArticleCategory;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ArticleCategoriesIndex extends Component
{
    public string $name = '';

    public string $slug = '';

    public ?string $description = null;

    public ?int $editingId = null;

    public ?int $confirmingDeleteId = null;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('article_categories', 'slug')->ignore($this->editingId),
            ],
            'description' => ['nullable', 'string'],
        ];
    }

    public function save(): void
    {
        $validated = $this->validate();

        if ($this->editingId) {
            ArticleCategory::findOrFail($this->editingId)->update($validated);
            session()->flash('success', 'تم تحديث تصنيف المقالات بنجاح');
        } else {
            ArticleCategory::create($validated);
            session()->flash('success', 'تم إنشاء تصنيف المقالات بنجاح');
        }

        $this->reset(['name', 'slug', 'description', 'editingId']);
    }

    public function edit(int $id): void
    {
        $category = ArticleCategory::findOrFail($id);

        $this->editingId = $category->id;
        $this->name = $category->name;
        $this->slug = $category->slug;
        $this->description = $category->description;
        $this->confirmingDeleteId = null;
        $this->resetValidation();
    }

    public function cancelEdit(): void
    {
        $this->reset(['name', 'slug', 'description', 'editingId']);
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

        $category = ArticleCategory::findOrFail($id);

        if ($this->editingId === $category->id) {
            $this->cancelEdit();
        }

        $category->delete();
        $this->confirmingDeleteId = null;
        session()->flash('success', 'تم حذف تصنيف المقالات بنجاح');
    }

    public function render()
    {
        return view('livewire.admin.article-categories.article-categories-index', [
            'categories' => ArticleCategory::withCount('articles')->orderBy('name')->get(),
        ]);
    }
}
