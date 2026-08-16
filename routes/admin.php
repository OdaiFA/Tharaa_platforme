<?php

use App\Http\Controllers\Admin\AdminAgeGroupController;
use App\Http\Controllers\Admin\AdminArticleController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminCourseController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminLessonController;
use App\Http\Controllers\Admin\AdminModuleController;
use App\Http\Controllers\Admin\AdminQuestionController;
use App\Http\Controllers\Admin\AdminQuizController;
use App\Http\Controllers\Admin\AdminStatisticsController;
use App\Http\Controllers\Admin\AdminUserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');

    // Users
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');

    // Courses
    Route::get('/courses', [AdminCourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/create', [AdminCourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [AdminCourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{course}/edit', [AdminCourseController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{course}', [AdminCourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course}', [AdminCourseController::class, 'destroy'])->name('courses.destroy');
    Route::post('/courses/{course}/restore', [AdminCourseController::class, 'restore'])->name('courses.restore');

    // Modules
    Route::get('/courses/{course}/modules', [AdminModuleController::class, 'index'])->name('modules.index');
    Route::post('/modules', [AdminModuleController::class, 'store'])->name('modules.store');
    Route::put('/modules/{module}', [AdminModuleController::class, 'update'])->name('modules.update');
    Route::delete('/modules/{module}', [AdminModuleController::class, 'destroy'])->name('modules.destroy');

    // Lessons
    Route::get('/modules/{module}/lessons', [AdminLessonController::class, 'index'])->name('lessons.index');
    Route::post('/lessons', [AdminLessonController::class, 'store'])->name('lessons.store');
    Route::put('/lessons/{lesson}', [AdminLessonController::class, 'update'])->name('lessons.update');
    Route::delete('/lessons/{lesson}', [AdminLessonController::class, 'destroy'])->name('lessons.destroy');

    // Quizzes
    Route::get('/lessons/{lesson}/quiz', [AdminQuizController::class, 'index'])->name('quizzes.index');
    Route::post('/quizzes', [AdminQuizController::class, 'store'])->name('quizzes.store');
    Route::put('/quizzes/{quiz}', [AdminQuizController::class, 'update'])->name('quizzes.update');
    Route::delete('/quizzes/{quiz}', [AdminQuizController::class, 'destroy'])->name('quizzes.destroy');

    // Questions
    Route::get('/quizzes/{quiz}/questions', [AdminQuestionController::class, 'index'])->name('questions.index');
    Route::post('/questions', [AdminQuestionController::class, 'store'])->name('questions.store');
    Route::put('/questions/{question}', [AdminQuestionController::class, 'update'])->name('questions.update');
    Route::delete('/questions/{question}', [AdminQuestionController::class, 'destroy'])->name('questions.destroy');

    // Articles
    Route::get('/articles', [AdminArticleController::class, 'index'])->name('articles.index');
    Route::get('/articles/create', [AdminArticleController::class, 'create'])->name('articles.create');
    Route::post('/articles', [AdminArticleController::class, 'store'])->name('articles.store');
    Route::get('/articles/{article}/edit', [AdminArticleController::class, 'edit'])->name('articles.edit');
    Route::put('/articles/{article}', [AdminArticleController::class, 'update'])->name('articles.update');
    Route::delete('/articles/{article}', [AdminArticleController::class, 'destroy'])->name('articles.destroy');
    Route::post('/articles/{article}/restore', [AdminArticleController::class, 'restore'])->name('articles.restore');

    // Categories (financial + article)
    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('/article-categories', [AdminCategoryController::class, 'articleCategories'])->name('article-categories.index');
    Route::post('/article-categories', [AdminCategoryController::class, 'storeArticleCategory'])->name('article-categories.store');
    Route::put('/article-categories/{category}', [AdminCategoryController::class, 'updateArticleCategory'])->name('article-categories.update');
    Route::delete('/article-categories/{category}', [AdminCategoryController::class, 'destroyArticleCategory'])->name('article-categories.destroy');

    // Age groups
    Route::get('/age-groups', [AdminAgeGroupController::class, 'index'])->name('age-groups.index');
    Route::post('/age-groups', [AdminAgeGroupController::class, 'store'])->name('age-groups.store');
    Route::put('/age-groups/{ageGroup}', [AdminAgeGroupController::class, 'update'])->name('age-groups.update');

    // Statistics
    Route::get('/statistics', AdminStatisticsController::class)->name('statistics');
});
