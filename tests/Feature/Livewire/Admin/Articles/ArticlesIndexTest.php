<?php

namespace Tests\Feature\Livewire\Admin\Articles;

use App\Livewire\Admin\Articles\ArticlesIndex;
use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ArticlesIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_user_cannot_access_admin_articles(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('admin.articles.index'))->assertForbidden();
    }

    public function test_admin_can_render_the_articles_page(): void
    {
        $admin = User::factory()->admin()->create();
        Article::factory()->create();

        $this->actingAs($admin)
            ->get(route('admin.articles.index'))
            ->assertOk()
            ->assertSeeLivewire(ArticlesIndex::class);
    }

    public function test_search_filters_by_title(): void
    {
        $admin = User::factory()->admin()->create();
        Article::factory()->create(['title' => 'أساسيات الادخار']);
        Article::factory()->create(['title' => 'مقال آخر']);

        Livewire::actingAs($admin)
            ->test(ArticlesIndex::class)
            ->set('search', 'الادخار')
            ->assertSee('أساسيات الادخار')
            ->assertDontSee('مقال آخر');
    }

    public function test_admin_can_delete_an_article_after_confirmation(): void
    {
        $admin = User::factory()->admin()->create();
        $article = Article::factory()->create();

        Livewire::actingAs($admin)
            ->test(ArticlesIndex::class)
            ->call('delete', $article->id);

        $this->assertNotSoftDeleted($article);

        Livewire::actingAs($admin)
            ->test(ArticlesIndex::class)
            ->call('confirmDelete', $article->id)
            ->call('delete', $article->id);

        $this->assertSoftDeleted($article);
    }

    public function test_admin_can_restore_a_deleted_article(): void
    {
        $admin = User::factory()->admin()->create();
        $article = Article::factory()->create();
        $article->delete();

        Livewire::actingAs($admin)
            ->test(ArticlesIndex::class)
            ->call('restore', $article->id);

        $this->assertNotSoftDeleted($article->fresh());
    }
}
