<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Data migration strategy (Strategy B — user's default currency),
     * confirmed safe by direct inspection before writing this migration:
     *   - 100% of existing goals (40/40) belong to users whose `users.currency`
     *     is 'SAR'.
     *   - The one existing goal_contribution that has a non-null `account_id`
     *     points to a SAR account, agreeing with its goal owner's currency.
     *   - No user who owns a goal has accounts in more than one currency.
     * Every available signal agrees, so backfilling from `users.currency` is
     * not a guess — it reproduces the only currency any existing goal's data
     * is actually consistent with. See docs/financial-hardening/GOAL_CURRENCY_ARCHITECTURE.md.
     */
    public function up(): void
    {
        Schema::table('goals', function (Blueprint $table) {
            $table->string('currency_code', 3)->nullable()->after('target_amount');
        });

        DB::table('goals')
            ->join('users', 'users.id', '=', 'goals.user_id')
            ->update(['goals.currency_code' => DB::raw('users.currency')]);

        Schema::table('goals', function (Blueprint $table) {
            $table->string('currency_code', 3)->default('SAR')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goals', function (Blueprint $table) {
            $table->dropColumn('currency_code');
        });
    }
};
