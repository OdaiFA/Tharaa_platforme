<?php

namespace App\Livewire\Admin\Articles;

use App\Models\Article;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ArticlesIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public bool $published = false;

    public ?int $confirmingDeleteId = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPublished(): void
    {
        $this->resetPage();
    }

    public function confirmDelete(int $articleId): void
    {
        $this->confirmingDeleteId = $articleId;
    }

    public function cancelDelete(): void
    {
        $this->confirmingDeleteId = null;
    }

    public function delete(int $articleId): void
    {
        if ($this->confirmingDeleteId !== $articleId) {
            return;
        }

        Article::findOrFail($articleId)->delete();
        $this->confirmingDeleteId = null;

        session()->flash('success', 'تم حذف المقال بنجاح');
    }

    public function restore(int $articleId): void
    {
        Article::withTrashed()->findOrFail($articleId)->restore();

        session()->flash('success', 'تمت استعادة المقال بنجاح');
    }

    public function render()
    {
        $articles = Article::query()
            ->with('category')
            ->withTrashed()
            ->when($this->search, fn ($q, $search) => $q->where('title', 'like', "%{$search}%"))
            ->when($this->published, fn ($q) => $q->where('is_published', true))
            ->latest()
            ->paginate(10);

        return view('livewire.admin.articles.articles-index', [
            'articles' => $articles,
        ]);
    }
}
