<?php

namespace App\Providers;

use App\Models\Enrollment;
use App\Models\GoalContribution;
use App\Models\LessonProgress;
use App\Models\QuizAttempt;
use App\Observers\EnrollmentObserver;
use App\Observers\GoalContributionObserver;
use App\Observers\LessonProgressObserver;
use App\Observers\QuizAttemptObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Enrollment::observe(EnrollmentObserver::class);
        GoalContribution::observe(GoalContributionObserver::class);
        LessonProgress::observe(LessonProgressObserver::class);
        QuizAttempt::observe(QuizAttemptObserver::class);
    }
}
