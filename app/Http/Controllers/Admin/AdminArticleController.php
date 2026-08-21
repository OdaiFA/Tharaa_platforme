<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreArticleRequest;
use App\Http\Requests\Admin\UpdateArticleRequest;
use App\Models\Article;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminArticleController extends Controller
{
    public function index(): View
    {
        return view('admin.articles.index');
    }

    public function create(): View
    {
        return view('admin.articles.create');
    }

    public function store(StoreArticleRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['featured_image']);

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('articles', 'public');
        }

        Article::create(array_merge($data, [
            'author_id' => auth()->id(),
            'published_at' => $request->boolean('is_published') ? now() : null,
        ]));

        return redirect()->route('admin.articles.index')->with('success', 'تم إنشاء المقال بنجاح');
    }

    public function edit(Article $article): View
    {
        return view('admin.articles.edit', compact('article'));
    }

    public function update(UpdateArticleRequest $request, Article $article): RedirectResponse
    {
        $data = $request->safe()->except(['featured_image']);

        if ($request->hasFile('featured_image')) {
            if ($article->featured_image) {
                Storage::disk('public')->delete($article->featured_image);
            }

            $data['featured_image'] = $request->file('featured_image')->store('articles', 'public');
        }

        $article->update(array_merge($data, [
            'published_at' => $request->boolean('is_published') ? ($article->published_at ?? now()) : $article->published_at,
        ]));

        return redirect()->route('admin.articles.index')->with('success', 'تم تحديث المقال بنجاح');
    }

    public function destroy(Article $article): RedirectResponse
    {
        $article->delete();

        return back()->with('success', 'تم حذف المقال بنجاح');
    }

    public function restore(int $id): RedirectResponse
    {
        Article::withTrashed()->findOrFail($id)->restore();

        return back()->with('success', 'تمت استعادة المقال بنجاح');
    }
}
