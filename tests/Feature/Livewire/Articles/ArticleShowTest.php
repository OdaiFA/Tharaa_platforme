<?php

namespace Tests\Feature\Livewire\Articles;

use App\Livewire\Articles\ArticleShow;
use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ArticleShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_a_published_article_and_views_count_increments(): void
    {
        $article = Article::factory()->create(['is_published' => true, 'views_count' => 5]);

        $this->get(route('articles.show', $article))
            ->assertOk()
            ->assertSeeLivewire(ArticleShow::class);

        $this->assertSame(6, $article->fresh()->views_count);
    }

    public function test_guest_cannot_view_an_unpublished_article(): void
    {
        $article = Article::factory()->create(['is_published' => false]);

        $this->get(route('articles.show', $article))->assertNotFound();
    }

    public function test_admin_can_view_an_unpublished_article(): void
    {
        $admin = User::factory()->admin()->create();
        $article = Article::factory()->create(['is_published' => false]);

        $this->actingAs($admin)
            ->get(route('articles.show', $article))
            ->assertOk();
    }

    public function test_viewing_an_unpublished_article_does_not_increment_views(): void
    {
        $admin = User::factory()->admin()->create();
        $article = Article::factory()->create(['is_published' => false, 'views_count' => 3]);

        Livewire::actingAs($admin)->test(ArticleShow::class, ['article' => $article]);

        $this->assertSame(3, $article->fresh()->views_count);
    }
}
