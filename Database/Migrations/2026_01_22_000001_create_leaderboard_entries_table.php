<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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
            $table->unsignedBigInteger('approved_by')->nullable()->index();
            $table->timestamp('approved_at')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();

            // enforce unique user constraint at DB level
            $table->unique('user_id', 'leaderboard_unique_user');

            // We'll create position uniqueness differently per driver (see below)
            // Foreign keys
            $table->foreign('user_id', 'leaderboard_entries_user_fk')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by', 'leaderboard_entries_approved_by_fk')->references('id')->on('users')->onDelete('set null');
        });

        // Add position uniqueness taking DB capabilities into account
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            // Postgres supports partial unique indexes
            DB::statement('CREATE UNIQUE INDEX leaderboard_unique_position_active ON leaderboard_entries (position) WHERE (is_active = true);');
        } else {
            // For MySQL/SQLite create composite unique index on (position, is_active)
            // This enforces uniqueness among active rows because is_active is part of the key.
            Schema::table('leaderboard_entries', function (Blueprint $table) {
                $table->unique(['position', 'is_active'], 'leaderboard_unique_position_active_comp');
            });
        }

        // Add CHECK constraint for position range where supported
        try {
            if ($driver === 'pgsql') {
                DB::statement('ALTER TABLE leaderboard_entries ADD CONSTRAINT leaderboard_position_range CHECK (position >= 1 AND position <= 20);');
            } elseif ($driver === 'mysql') {
                // MySQL supports CHECK syntactically from 8.0.16 (may be ignored on older versions)
                DB::statement('ALTER TABLE leaderboard_entries ADD CONSTRAINT leaderboard_position_range CHECK (position >= 1 AND position <= 20);');
            }
            // SQLite typically ignores CHECK or supports it; leaving as best-effort
        } catch (\Throwable $e) {
            // If the DB does not support adding check via statement, continue (application should still validate)
        }
    }

    public function down()
    {
        $driver = Schema::getConnection()->getDriverName();

        // Drop partial/indexes if exist
        if ($driver === 'pgsql') {
            // drop partial index if exists
            try {
                DB::statement('DROP INDEX IF EXISTS leaderboard_unique_position_active;');
            } catch (\Throwable $e) {
                // ignore
            }
            // drop check constraint if exists
            try {
                DB::statement('ALTER TABLE leaderboard_entries DROP CONSTRAINT IF EXISTS leaderboard_position_range;');
            } catch (\Throwable $e) {
                // ignore
            }
        } else {
            // MySQL / SQLite
            Schema::table('leaderboard_entries', function (Blueprint $table) {
                // drop composite unique if exists
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                // Attempt drop safely
                try {
                    $table->dropUnique('leaderboard_unique_position_active_comp');
                } catch (\Throwable $e) {
                    // ignore if not exists
                }
            });
            // try drop check constraint (MySQL)
            try {
                DB::statement('ALTER TABLE leaderboard_entries DROP CHECK IF EXISTS leaderboard_position_range;');
            } catch (\Throwable $e) {
                // ignore
            }
        }

        // Drop table will cascade drop foreign keys and other indexes
        Schema::dropIfExists('leaderboard_entries');
    }
}
