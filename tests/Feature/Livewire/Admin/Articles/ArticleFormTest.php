<?php

namespace Tests\Feature\Livewire\Admin\Articles;

use App\Livewire\Admin\Articles\ArticleForm;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ArticleFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_user_cannot_access_the_create_form(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('admin.articles.create'))->assertForbidden();
    }

    public function test_admin_can_create_a_published_article(): void
    {
        $admin = User::factory()->admin()->create();
        $category = ArticleCategory::factory()->create();

        Livewire::actingAs($admin)
            ->test(ArticleForm::class)
            ->set('title', 'مقال جديد')
            ->set('content', 'محتوى المقال')
            ->set('category_id', $category->id)
            ->set('is_published', true)
            ->call('save')
            ->assertHasNoErrors();

        $article = Article::where('title', 'مقال جديد')->first();
        $this->assertNotNull($article);
        $this->assertSame($admin->id, $article->author_id);
        $this->assertTrue($article->is_published);
        $this->assertNotNull($article->published_at);
    }

    public function test_required_fields_are_validated(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(ArticleForm::class)
            ->set('title', '')
            ->set('content', '')
            ->call('save')
            ->assertHasErrors(['title', 'content']);
    }

    public function test_admin_can_upload_a_featured_image(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(ArticleForm::class)
            ->set('title', 'مقال بصورة')
            ->set('content', 'محتوى')
            ->set('featured_image', UploadedFile::fake()->image('cover.jpg'))
            ->call('save')
            ->assertHasNoErrors();

        $article = Article::where('title', 'مقال بصورة')->first();
        Storage::disk('public')->assertExists($article->featured_image);
    }

    public function test_admin_can_edit_an_existing_article(): void
    {
        $admin = User::factory()->admin()->create();
        $article = Article::factory()->create(['title' => 'قديم']);

        Livewire::actingAs($admin)
            ->test(ArticleForm::class, ['articleId' => $article->id])
            ->assertSet('title', 'قديم')
            ->set('title', 'محدث')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('محدث', $article->fresh()->title);
    }
}
