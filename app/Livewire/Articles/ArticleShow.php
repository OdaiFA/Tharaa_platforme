<?php

namespace App\Livewire\Articles;

use App\Models\Article;
use Livewire\Component;

class ArticleShow extends Component
{
    public Article $article;

    public function mount(Article $article): void
    {
        if (! $article->is_published && ! auth()->user()?->isAdmin()) {
            abort(404);
        }

        if ($article->is_published) {
            $article->increment('views_count');
        }

        $this->article = $article;
    }

    public function render()
    {
        return view('livewire.articles.article-show');
    }
}
