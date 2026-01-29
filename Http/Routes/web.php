<?php

use Illuminate\Support\Facades\Route;
use Modules\Rank\Http\Controllers\LeaderboardController;
use Modules\Rank\Entities\Entry;
use Modules\Rank\Http\Controllers\AmbassadorController;
use YooKassa\Request\Payments\Leg;

// LEADERBOARD ROUTES
Route::group(['prefix' => 'admin', 'middleware' => [
    'auth:admin',
    'setlang',
], 'as' => 'admin.'], function () {
    // Leaderboard Management
    Route::group(['prefix' => 'leaderboard', 'middleware' => ['can:manage-leaderboard']], function () {
        Route::get('/', [LeaderboardController::class, 'admin_index'])->name('leaderboard.all');
        Route::post('/generate', [LeaderboardController::class, 'generateCandidates'])->name('leaderboard.generate');
        Route::post('/approve', [LeaderboardController::class, 'approve'])->name('leaderboard.approve');
        Route::match(['POST', 'DELETE'], '/remove', [LeaderboardController::class, 'remove'])->name('leaderboard.remove');
        Route::post('/bulk-actions', [LeaderboardController::class, 'bulk_actions'])->name('leaderboard.bulk_actions');
    });

    // Ambassador Management
    Route::group(['prefix' => 'ambassador', 'middleware' => ['can:manage-ambassadors']], function () {
        Route::get('/', [AmbassadorController::class, 'admin_index'])->name('ambassador.all');
        Route::post('/approve', [AmbassadorController::class, 'approve_as_ambassador'])->name('ambassador.approve');
        Route::post('/set-admin', [AmbassadorController::class, 'set_as_admin'])->name('ambassador.set_admin');
        Route::post('/bulk-actions', [AmbassadorController::class, 'bulk_actions'])->name('ambassador.bulk_actions');
        Route::post('/delete/{id}', [AmbassadorController::class, 'delete_ambassador'])->name('ambassador.delete');
    });
});

// Frontend routes (For user)
Route::group(['middleware' => ['globalVariable', 'maintains_mode', 'setlang'], 'as' => 'user.'], function () {
    // Leaderboard View
    Route::get('/leaderboard', [LeaderboardController::class, 'user_index'])->name('leaderboard.index');

    // Ambassador Form Display & Submission route
    Route::group(['middleware' => ['auth', 'userEmailVerify', 'Google2FA', 'identityVerified']], function () {
        Route::get('/become-an-ambassador', [AmbassadorController::class, 'user_form'])->name('ambassador.form');
        Route::post('/ambassador-submit', [AmbassadorController::class, 'submit_form'])->name('ambassador.store');
    });
});
