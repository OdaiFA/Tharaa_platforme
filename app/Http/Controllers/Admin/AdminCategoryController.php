<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Models\ArticleCategory;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminCategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.categories.index');
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        Category::create(array_merge($request->validated(), ['is_system' => false]));

        return back()->with('success', 'تم إنشاء التصنيف بنجاح');
    }

    public function update(StoreCategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update($request->validated());

        return back()->with('success', 'تم تحديث التصنيف بنجاح');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->is_system) {
            abort(403, 'لا يمكن حذف التصنيفات النظامية');
        }

        $category->delete();

        return back()->with('success', 'تم حذف التصنيف بنجاح');
    }

    /* ------------------------- Article categories ------------------------- */

    public function articleCategories(): View
    {
        return view('admin.categories.articles');
    }

    public function storeArticleCategory(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:article_categories,slug'],
            'description' => ['nullable', 'string'],
        ]);

        ArticleCategory::create($data);

        return back()->with('success', 'تم إنشاء تصنيف المقالات بنجاح');
    }

    public function updateArticleCategory(Request $request, ArticleCategory $category): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:article_categories,slug,' . $category->id],
            'description' => ['nullable', 'string'],
        ]);

        $category->update($data);

        return back()->with('success', 'تم تحديث تصنيف المقالات بنجاح');
    }

    public function destroyArticleCategory(ArticleCategory $category): RedirectResponse
    {
        $category->delete();

        return back()->with('success', 'تم حذف تصنيف المقالات بنجاح');
    }
}
