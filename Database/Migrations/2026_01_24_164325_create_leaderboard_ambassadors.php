<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leaderboard_ambassadors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->longText('notes')->nullable();
            $table->boolean('is_ambassador')->default(false);
            $table->unsignedBigInteger('approved_by')->nullable()->index();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id', 'leaderboard_ambassadors_user_fk')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by', 'leaderboard_ambassadors_approved_by_fk')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        // Drop foreign keys safely then table
        if (Schema::hasTable('leaderboard_ambassadors')) {
            Schema::table('leaderboard_ambassadors', function (Blueprint $table) {
                try {
                    $table->dropForeign('leaderboard_ambassadors_user_fk');
                } catch (\Throwable $e) {
                }
                try {
                    $table->dropForeign('leaderboard_ambassadors_approved_by_fk');
                } catch (\Throwable $e) {
                }
            });
            Schema::dropIfExists('leaderboard_ambassadors');
        }
    }
};
