<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ambassadors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('fullname', 100);
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('address')->nullable();
            $table->string('school')->nullable();
            $table->string('level')->nullable();
            $table->string('course')->nullable();
            $table->longText('reason');
            $table->longText('notes')->nullable();
            $table->boolean('is_ambassador')->default(false);
            $table->unsignedBigInteger('approved_by')->nullable()->index();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id', 'ambassadors_user_fk')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by', 'ambassadors_approved_by_fk')->references('id')->on('admins')->onDelete('set null');
        });
    }

    public function down(): void
    {
        // Drop foreign keys safely then table
        if (Schema::hasTable('ambassadors')) {
            Schema::table('ambassadors', function (Blueprint $table) {
                try {
                    $table->dropForeign('ambassadors_user_fk');
                } catch (\Throwable $e) {
                }
                try {
                    $table->dropForeign('ambassadors_approved_by_fk');
                } catch (\Throwable $e) {
                }
            });
            Schema::dropIfExists('ambassadors');
        }
    }
};
