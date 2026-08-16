<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Repositories\ArticleRepository;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function __construct(private readonly ArticleRepository $articles) {}

    public function index(): View
    {
        $articles = $this->articles->published([
            'category_id' => request('category_id'),
        ]);

        $categories = ArticleCategory::query()->withCount('articles')->orderBy('name')->get();

        return view('articles.index', compact('articles', 'categories'));
    }

    public function show(Article $article): View
    {
        if (! $article->is_published && ! auth()->user()?->isAdmin()) {
            abort(404);
        }

        if ($article->is_published) {
            $article->increment('views_count');
        }

        return view('articles.show', compact('article'));
    }
}
