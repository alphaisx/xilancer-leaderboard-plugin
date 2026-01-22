<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaderboardCandidatesTable extends Migration
{
    public function up()
    {
        Schema::create('leaderboard_candidates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->json('metrics')->nullable();
            $table->double('score')->default(0);
            $table->timestamp('computed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('leaderboard_candidates');
    }
}
