<?php

namespace App\Livewire\Admin\Articles;

use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class ArticleForm extends Component
{
    use WithFileUploads;

    public ?int $articleId = null;

    public string $title = '';

    public ?string $excerpt = null;

    public string $content = '';

    public ?int $category_id = null;

    public $featured_image = null;

    public ?string $existingFeaturedImage = null;

    public bool $is_published = false;

    public function mount(?int $articleId = null): void
    {
        if ($articleId) {
            $article = Article::findOrFail($articleId);

            $this->articleId = $article->id;
            $this->title = $article->title;
            $this->excerpt = $article->excerpt;
            $this->content = $article->content;
            $this->category_id = $article->category_id;
            $this->existingFeaturedImage = $article->featured_image;
            $this->is_published = $article->is_published;
        }
    }

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'featured_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'category_id' => ['nullable', 'exists:article_categories,id'],
        ];
    }

    protected function messages(): array
    {
        return [
            'title.required' => 'عنوان المقال مطلوب',
            'content.required' => 'محتوى المقال مطلوب',
            'featured_image.image' => 'يجب أن تكون الصورة صورة',
            'featured_image.mimes' => 'صيغ الصور المسموحة: jpg, jpeg, png',
            'featured_image.max' => 'حجم الصورة يجب أن يكون أقل من 2 ميجابايت',
            'category_id.exists' => 'التصنيف غير موجود',
        ];
    }

    public function save(): mixed
    {
        $validated = $this->validate();
        unset($validated['featured_image']);

        if ($this->articleId) {
            $article = Article::findOrFail($this->articleId);

            if ($this->featured_image) {
                if ($article->featured_image) {
                    Storage::disk('public')->delete($article->featured_image);
                }
                $validated['featured_image'] = $this->featured_image->store('articles', 'public');
            }

            $article->update(array_merge($validated, [
                'is_published' => $this->is_published,
                'published_at' => $this->is_published ? ($article->published_at ?? now()) : $article->published_at,
            ]));

            session()->flash('success', 'تم تحديث المقال بنجاح');
        } else {
            if ($this->featured_image) {
                $validated['featured_image'] = $this->featured_image->store('articles', 'public');
            }

            Article::create(array_merge($validated, [
                'author_id' => auth()->id(),
                'is_published' => $this->is_published,
                'published_at' => $this->is_published ? now() : null,
            ]));

            session()->flash('success', 'تم إنشاء المقال بنجاح');
        }

        return redirect()->route('admin.articles.index');
    }

    public function render()
    {
        return view('livewire.admin.articles.article-form', [
            'categories' => ArticleCategory::all(),
        ]);
    }
}
