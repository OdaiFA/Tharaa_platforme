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
        Schema::create('age_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->integer('min_age');
            $table->integer('max_age');
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('age_group_id')
                ->references('id')
                ->on('age_groups')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['age_group_id']);
        });

        Schema::dropIfExists('age_groups');
    }
};
