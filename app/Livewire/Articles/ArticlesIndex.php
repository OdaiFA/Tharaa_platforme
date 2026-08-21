<?php

namespace App\Livewire\Articles;

use App\Models\ArticleCategory;
use App\Repositories\ArticleRepository;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ArticlesIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $category_id = '';

    public function updatedCategoryId(): void
    {
        $this->resetPage();
    }

    public function render(ArticleRepository $articles)
    {
        return view('livewire.articles.articles-index', [
            'articles' => $articles->published([
                'category_id' => $this->category_id ?: null,
            ]),
            'categories' => ArticleCategory::query()->withCount('articles')->orderBy('name')->get(),
        ]);
    }
}
