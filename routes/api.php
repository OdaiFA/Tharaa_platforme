<?php

use App\Http\Controllers\Api\ApiAccountController;
use App\Http\Controllers\Api\ApiArticleController;
use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiBudgetController;
use App\Http\Controllers\Api\ApiCourseController;
use App\Http\Controllers\Api\ApiGoalController;
use App\Http\Controllers\Api\ApiNotificationController;
use App\Http\Controllers\Api\ApiProfileController;
use App\Http\Controllers\Api\ApiQuizController;
use App\Http\Controllers\Api\ApiTransactionController;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    Route::post('/register', [ApiAuthController::class, 'register'])->name('register');
    Route::post('/login', [ApiAuthController::class, 'login'])->middleware('throttle:5,1')->name('login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [ApiAuthController::class, 'logout'])->name('logout');
        Route::get('/user', [ApiAuthController::class, 'user'])->name('user');
        Route::put('/user/profile', [ApiProfileController::class, 'update'])->name('user.profile');

        Route::apiResource('accounts', ApiAccountController::class);
        Route::apiResource('transactions', ApiTransactionController::class)->except(['show']);
        Route::apiResource('budgets', ApiBudgetController::class);
        Route::apiResource('goals', ApiGoalController::class)->except(['update'])->parameters(['goals' => 'goal']);
        Route::put('/goals/{goal}', [ApiGoalController::class, 'update'])->name('goals.update');
        Route::post('/goals/{goal}/contribute', [ApiGoalController::class, 'contribute'])->name('goals.contribute');

        Route::get('/courses/recommended', [ApiCourseController::class, 'recommended'])->name('courses.recommended');
        Route::post('/courses/{course}/enroll', [ApiCourseController::class, 'enroll'])->name('courses.enroll');

        Route::post('/quizzes/{quiz}/submit', [ApiQuizController::class, 'submit'])->name('quizzes.submit');

        Route::get('/notifications', [ApiNotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{notification}/read', [ApiNotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::put('/notifications/read-all', [ApiNotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    });

    Route::get('/courses', [ApiCourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{course}', [ApiCourseController::class, 'show'])->name('courses.show');
    Route::get('/articles', [ApiArticleController::class, 'index'])->name('articles.index');
    Route::get('/articles/{article}', [ApiArticleController::class, 'show'])->name('articles.show');
});
