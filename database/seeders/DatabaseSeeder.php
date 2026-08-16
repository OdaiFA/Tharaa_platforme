<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\AgeGroup;
use App\Models\Budget;
use App\Models\BudgetCategory;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Goal;
use App\Models\GoalContribution;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Module;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            AgeGroupSeeder::class,
            CategorySeeder::class,
            ArticleCategorySeeder::class,
        ]);

        $admin = User::firstOrCreate(
            ['email' => 'admin@tharaa.sa'],
            [
                'name' => 'مدير المنصة',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
                'is_active' => true,
                'currency' => 'SAR',
                'financial_level' => 'advanced',
            ],
        );

        $admin->assignAgeGroup();

        User::factory(20)->create()->each(function (User $user) {
            $user->assignAgeGroup();

            Account::factory(2)->create(['user_id' => $user->id])->each(function (Account $account) use ($user) {
                Transaction::factory(fake()->numberBetween(10, 40))
                    ->income()
                    ->create([
                        'user_id' => $user->id,
                        'account_id' => $account->id,
                        'transaction_date' => fake()->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
                    ]);

                Transaction::factory(fake()->numberBetween(20, 60))
                    ->expense()
                    ->create([
                        'user_id' => $user->id,
                        'account_id' => $account->id,
                        'transaction_date' => fake()->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
                    ]);
            });

            Goal::factory(2)->create(['user_id' => $user->id])->each(function (Goal $goal) {
                GoalContribution::factory(fake()->numberBetween(1, 5))->create([
                    'goal_id' => $goal->id,
                    'user_id' => $goal->user_id,
                ]);
            });

            $budget = Budget::factory()->create(['user_id' => $user->id]);

            $expenseCategories = \App\Models\Category::query()->expense()->inRandomOrder()->limit(3)->get();

            foreach ($expenseCategories as $category) {
                BudgetCategory::factory()->create([
                    'budget_id' => $budget->id,
                    'category_id' => $category->id,
                ]);
            }
        });

        $courses = Course::factory(4)->create(['created_by' => $admin->id])->each(function (Course $course) use ($admin) {
            $course->ageGroups()->sync(AgeGroup::inRandomOrder()->limit(2)->pluck('id'));

            $course->modules()->saveMany(
                Module::factory(2)->create(['course_id' => $course->id]),
            )->each(function (Module $module) {
                $module->lessons()->saveMany(
                    Lesson::factory(3)->create(['module_id' => $module->id]),
                )->each(function (Lesson $lesson) {
                    $quiz = Quiz::factory()->create(['lesson_id' => $lesson->id]);
                    $quiz->questions()->saveMany(
                        QuizQuestion::factory(4)->create(['quiz_id' => $quiz->id]),
                    );
                });
            });
        });

        User::where('role', 'user')->inRandomOrder()->take(15)->get()->each(function (User $user) use ($courses) {
            $course = $courses->random();

            $enrollment = Enrollment::factory()->create([
                'user_id' => $user->id,
                'course_id' => $course->id,
            ]);

            $course->modules->each(function (Module $module) use ($enrollment, $user) {
                $module->lessons->each(function (Lesson $lesson) use ($enrollment, $user) {
                    LessonProgress::create([
                        'user_id' => $user->id,
                        'lesson_id' => $lesson->id,
                        'enrollment_id' => $enrollment->id,
                        'status' => fake()->boolean(70) ? 'completed' : 'in_progress',
                        'completed_at' => fake()->boolean(70) ? now() : null,
                    ]);

                    if ($lesson->quiz && fake()->boolean(60)) {
                        QuizAttempt::factory()->create([
                            'user_id' => $user->id,
                            'quiz_id' => $lesson->quiz->id,
                            'enrollment_id' => $enrollment->id,
                        ]);
                    }
                });
            });
        });

        \App\Models\Article::factory(8)->create(['author_id' => $admin->id]);
    }
}
