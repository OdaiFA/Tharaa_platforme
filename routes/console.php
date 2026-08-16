<?php

use App\Jobs\ProcessRecurringTransactions;
use App\Jobs\SendCourseProgressReminders;
use App\Jobs\SendGoalDeadlineReminders;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new ProcessRecurringTransactions)->dailyAt('00:01');
Schedule::job(new SendGoalDeadlineReminders)->dailyAt('00:05');
Schedule::job(new SendCourseProgressReminders)->dailyAt('00:10');
