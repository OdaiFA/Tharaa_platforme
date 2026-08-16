<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->json('notification_channels')->nullable();
            $table->string('language', 5)->default('ar');
            $table->enum('theme', ['light', 'dark'])->default('light');
            $table->string('default_currency', 3)->default('SAR');
            $table->boolean('budget_alert_enabled')->default(true);
            $table->boolean('goal_reminder_enabled')->default(true);
            $table->boolean('course_reminder_enabled')->default(true);
            $table->time('reminder_time')->default('20:00:00');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_settings');
    }
};
