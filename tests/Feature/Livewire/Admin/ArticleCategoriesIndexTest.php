<?php

namespace Tests\Feature\Livewire\Admin;

use App\Livewire\Admin\ArticleCategories\ArticleCategoriesIndex;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ArticleCategoriesIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_render_the_component(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.article-categories.index'))
            ->assertOk()
            ->assertSeeLivewire(ArticleCategoriesIndex::class);
    }

    public function test_regular_user_cannot_access_it(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.article-categories.index'))
            ->assertForbidden();
    }

    public function test_admin_can_create_category(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(ArticleCategoriesIndex::class)
            ->set('name', 'التقاعد')
            ->set('slug', 'retirement')
            ->set('description', 'التخطيط للتقاعد')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('article_categories', [
            'name' => 'التقاعد',
            'slug' => 'retirement',
        ]);
    }

    public function test_required_validation_works(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(ArticleCategoriesIndex::class)
            ->set('name', '')
            ->set('slug', '')
            ->call('save')
            ->assertHasErrors(['name' => 'required', 'slug' => 'required']);

        $this->assertDatabaseCount('article_categories', 0);
    }

    public function test_duplicate_slug_is_rejected_on_create(): void
    {
        $admin = User::factory()->admin()->create();
        ArticleCategory::factory()->create(['slug' => 'saving']);

        Livewire::actingAs($admin)
            ->test(ArticleCategoriesIndex::class)
            ->set('name', 'تصنيف آخر')
            ->set('slug', 'saving')
            ->call('save')
            ->assertHasErrors(['slug' => 'unique']);

        $this->assertDatabaseCount('article_categories', 1);
    }

    public function test_admin_can_edit_category(): void
    {
        $admin = User::factory()->admin()->create();
        $category = ArticleCategory::factory()->create(['name' => 'الادخار', 'slug' => 'saving']);

        Livewire::actingAs($admin)
            ->test(ArticleCategoriesIndex::class)
            ->call('edit', $category->id)
            ->assertSet('name', 'الادخار')
            ->assertSet('slug', 'saving')
            ->set('name', 'الادخار الذكي')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('article_categories', [
            'id' => $category->id,
            'name' => 'الادخار الذكي',
            'slug' => 'saving',
        ]);
    }

    public function test_existing_own_slug_is_allowed_during_edit(): void
    {
        $admin = User::factory()->admin()->create();
        $category = ArticleCategory::factory()->create(['name' => 'الاستثمار', 'slug' => 'investing']);

        Livewire::actingAs($admin)
            ->test(ArticleCategoriesIndex::class)
            ->call('edit', $category->id)
            ->set('description', 'وصف محدث')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('article_categories', [
            'id' => $category->id,
            'slug' => 'investing',
            'description' => 'وصف محدث',
        ]);
    }

    public function test_changing_slug_to_an_existing_categorys_slug_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        ArticleCategory::factory()->create(['slug' => 'budgeting']);
        $category = ArticleCategory::factory()->create(['slug' => 'saving']);

        Livewire::actingAs($admin)
            ->test(ArticleCategoriesIndex::class)
            ->call('edit', $category->id)
            ->set('slug', 'budgeting')
            ->call('save')
            ->assertHasErrors(['slug' => 'unique']);

        $this->assertDatabaseHas('article_categories', ['id' => $category->id, 'slug' => 'saving']);
    }

    public function test_delete_succeeds_when_allowed(): void
    {
        $admin = User::factory()->admin()->create();
        $category = ArticleCategory::factory()->create();

        Livewire::actingAs($admin)
            ->test(ArticleCategoriesIndex::class)
            ->call('confirmDelete', $category->id)
            ->call('delete', $category->id);

        $this->assertDatabaseMissing('article_categories', ['id' => $category->id]);
    }

    public function test_delete_requires_prior_confirmation(): void
    {
        $admin = User::factory()->admin()->create();
        $category = ArticleCategory::factory()->create();

        Livewire::actingAs($admin)
            ->test(ArticleCategoriesIndex::class)
            ->call('delete', $category->id);

        $this->assertDatabaseHas('article_categories', ['id' => $category->id]);
    }

    public function test_deleting_a_category_nulls_its_articles_category_instead_of_deleting_them(): void
    {
        $admin = User::factory()->admin()->create();
        $author = User::factory()->admin()->create();
        $category = ArticleCategory::factory()->create();
        $article = Article::factory()->for($author, 'author')->create(['category_id' => $category->id]);

        Livewire::actingAs($admin)
            ->test(ArticleCategoriesIndex::class)
            ->call('confirmDelete', $category->id)
            ->call('delete', $category->id);

        $this->assertDatabaseMissing('article_categories', ['id' => $category->id]);
        $this->assertDatabaseHas('articles', ['id' => $article->id, 'category_id' => null]);
        $this->assertNotSoftDeleted($article);
    }

    public function test_cancel_edit_resets_form_state(): void
    {
        $admin = User::factory()->admin()->create();
        $category = ArticleCategory::factory()->create(['name' => 'الادخار', 'slug' => 'saving']);

        Livewire::actingAs($admin)
            ->test(ArticleCategoriesIndex::class)
            ->call('edit', $category->id)
            ->assertSet('editingId', $category->id)
            ->call('cancelEdit')
            ->assertSet('editingId', null)
            ->assertSet('name', '')
            ->assertSet('slug', '');
    }
}
