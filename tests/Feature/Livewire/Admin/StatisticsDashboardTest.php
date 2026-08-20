<?php

namespace Tests\Feature\Livewire\Admin;

use App\Livewire\Admin\Statistics\StatisticsDashboard;
use App\Models\Account;
use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class StatisticsDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_render_statistics_component(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.statistics'))
            ->assertOk()
            ->assertSeeLivewire(StatisticsDashboard::class);
    }

    public function test_regular_user_cannot_access_it(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.statistics'))
            ->assertForbidden();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('admin.statistics'))
            ->assertRedirect(route('login'));
    }

    public function test_users_by_role_metric_is_correct(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->admin()->create();
        User::factory()->count(3)->create();

        $component = Livewire::actingAs($admin)->test(StatisticsDashboard::class);

        $usersByRole = $component->viewData('usersByRole');
        $this->assertSame(2, $usersByRole['admin']);
        $this->assertSame(3, $usersByRole['user']);
    }

    public function test_enrollment_by_course_aggregate_is_correct(): void
    {
        $admin = User::factory()->admin()->create();
        $popular = Course::factory()->create(['title' => 'دورة شائعة']);
        $quiet = Course::factory()->create(['title' => 'دورة هادئة']);
        Enrollment::factory()->for($popular)->count(3)->create();
        Enrollment::factory()->for($quiet)->count(1)->create();

        $component = Livewire::actingAs($admin)->test(StatisticsDashboard::class);

        $byCourse = $component->viewData('enrollmentsByCourse');
        $this->assertSame('دورة شائعة', $byCourse->first()->title);
        $this->assertSame(3, $byCourse->first()->enrollments_count);
    }

    public function test_financial_monthly_aggregate_is_correct(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();
        $account = Account::factory()->for($user)->create();
        $category = Category::factory()->create(['type' => 'income']);

        Transaction::factory()->for($user)->for($account)->create([
            'category_id' => $category->id,
            'type' => 'income',
            'amount' => 100,
            'transaction_date' => now()->startOfMonth(),
        ]);
        Transaction::factory()->for($user)->for($account)->create([
            'category_id' => $category->id,
            'type' => 'income',
            'amount' => 50,
            'transaction_date' => now()->startOfMonth(),
        ]);

        $component = Livewire::actingAs($admin)->test(StatisticsDashboard::class);

        $activity = $component->viewData('financialActivity');
        $month = now()->format('Y-m');
        $row = $activity->firstWhere('month', $month);
        $this->assertNotNull($row);
        $this->assertEquals(150, (float) $row->total);
    }

    public function test_quiz_pass_rate_aggregate_is_correct(): void
    {
        $admin = User::factory()->admin()->create();
        $course = Course::factory()->create();
        $module = Module::factory()->for($course)->create();
        $lesson = Lesson::factory()->for($module)->create();
        $quiz = Quiz::factory()->for($lesson)->create();
        $student = User::factory()->create();
        $enrollment = Enrollment::factory()->for($student)->for($course)->create();

        QuizAttempt::factory()->for($quiz)->for($student)->count(3)->create(['is_passed' => true, 'enrollment_id' => $enrollment->id]);
        QuizAttempt::factory()->for($quiz)->for($student)->count(1)->create(['is_passed' => false, 'enrollment_id' => $enrollment->id]);

        $component = Livewire::actingAs($admin)->test(StatisticsDashboard::class);

        $quizStats = $component->viewData('quizStats');
        $this->assertSame(4, $quizStats['attempts']);
        $this->assertSame(3, $quizStats['passed']);
        $this->assertSame(75.0, $quizStats['pass_rate']);
    }

    public function test_top_expense_categories_aggregate_is_correct(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();
        $account = Account::factory()->for($user)->create();
        $food = Category::factory()->create(['type' => 'expense', 'name' => 'طعام']);
        $transport = Category::factory()->create(['type' => 'expense', 'name' => 'مواصلات']);

        Transaction::factory()->for($user)->for($account)->create([
            'category_id' => $food->id, 'type' => 'expense', 'amount' => 300,
        ]);
        Transaction::factory()->for($user)->for($account)->create([
            'category_id' => $transport->id, 'type' => 'expense', 'amount' => 100,
        ]);

        $component = Livewire::actingAs($admin)->test(StatisticsDashboard::class);

        $topCategories = $component->viewData('topCategories');
        $this->assertSame('طعام', $topCategories->first()->name);
        $this->assertEquals(300, (float) $topCategories->first()->total);
    }

    public function test_empty_dataset_renders_safely(): void
    {
        $admin = User::factory()->admin()->create();

        $component = Livewire::actingAs($admin)->test(StatisticsDashboard::class);

        $component->assertOk();
        $this->assertTrue($component->viewData('enrollmentsByStatus')->isEmpty());
        $this->assertTrue($component->viewData('financialActivity')->isEmpty());
        $this->assertTrue($component->viewData('topCategories')->isEmpty());
        $this->assertSame(0, $component->viewData('quizStats')['attempts']);
        $component->assertSee('لا توجد بيانات');
    }

    public function test_no_private_record_level_data_is_exposed(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->create(['name' => 'خصوصية المستخدم', 'email' => 'private-user@example.com']);
        $account = Account::factory()->for($target)->create();
        Transaction::factory()->for($target)->for($account)->create(['amount' => 250]);

        $response = $this->actingAs($admin)->get(route('admin.statistics'));

        $response->assertOk();
        $response->assertDontSee('private-user@example.com');
        $response->assertDontSee('خصوصية المستخدم');
    }
}
