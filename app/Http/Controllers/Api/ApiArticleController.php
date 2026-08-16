<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiArticleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $articles = Article::query()
            ->published()
            ->with('category', 'author')
            ->when($request->input('category_id'), fn ($q, $id) => $q->forCategory($id))
            ->when($request->input('search'), fn ($q, $term) => $q->where('title', 'like', "%{$term}%"))
            ->latest('published_at')
            ->paginate(12);

        return ArticleResource::collection($articles);
    }

    public function show(Request $request, Article $article): JsonResponse
    {
        if (! $article->is_published && ! $request->user()?->isAdmin()) {
            abort(404);
        }

        if ($article->is_published) {
            $article->increment('views_count');
        }

        return new ArticleResource($article->load('category', 'author'));
    }
}
