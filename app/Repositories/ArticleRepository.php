<?php

namespace App\Repositories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Builder;

class ArticleRepository
{
    public function published(?array $filters = [])
    {
        return Article::query()
            ->published()
            ->with(['category', 'author'])
            ->when($filters['category_id'] ?? null, fn (Builder $q, $id) => $q->forCategory($id))
            ->when($filters['search'] ?? null, fn (Builder $q, $term) => $q->where('title', 'like', "%{$term}%"))
            ->latest('published_at')
            ->paginate(9);
    }

    public function findPublished(int $id): ?Article
    {
        return Article::query()->published()->with(['category', 'author'])->find($id);
    }
}
