<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaderboardEntriesTable extends Migration
{
    public function up()
    {
        Schema::create('leaderboard_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedTinyInteger('position')->index(); // 1-20
            $table->json('metrics_snapshot')->nullable();
            $table->double('score_snapshot')->default(0);
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['position'], 'unique_position');
        });
    }

    public function down()
    {
        Schema::dropIfExists('leaderboard_entries');
    }
}
