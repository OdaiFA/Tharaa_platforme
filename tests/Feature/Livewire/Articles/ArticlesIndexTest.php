<?php

namespace Tests\Feature\Livewire\Articles;

use App\Livewire\Articles\ArticlesIndex;
use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ArticlesIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_published_articles(): void
    {
        Article::factory()->create(['title' => 'منشور', 'is_published' => true]);
        Article::factory()->create(['title' => 'مسودة', 'is_published' => false]);

        $response = $this->get(route('articles.index'));

        $response->assertOk()->assertSeeLivewire(ArticlesIndex::class);
        $response->assertSee('منشور');
        $response->assertDontSee('مسودة');
    }

    public function test_category_filter_scopes_results(): void
    {
        $categoryA = ArticleCategory::factory()->create();
        $categoryB = ArticleCategory::factory()->create();
        Article::factory()->create(['title' => 'مقال أ', 'category_id' => $categoryA->id, 'is_published' => true]);
        Article::factory()->create(['title' => 'مقال ب', 'category_id' => $categoryB->id, 'is_published' => true]);

        Livewire::test(ArticlesIndex::class)
            ->set('category_id', (string) $categoryA->id)
            ->assertSee('مقال أ')
            ->assertDontSee('مقال ب');
    }
}
