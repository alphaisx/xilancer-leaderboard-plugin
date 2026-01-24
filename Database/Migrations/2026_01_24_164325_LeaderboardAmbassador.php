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
        Schema::table('leaderboard_entries', function (Blueprint $table): void {
            // Add Ambassador Columns
            $table->string('referral_code')->nullable()->after('ambassador_id');
            $table->index('referral_code', 'leaderboard_ambassadors_referral_code_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
