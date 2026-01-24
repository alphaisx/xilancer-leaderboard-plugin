<?php

use Illuminate\Support\Facades\Route;
use Modules\Leaderboard\Http\Controllers\Admin\LeaderboardController;
use Modules\Leaderboard\Entities\Entry;

Route::group(['prefix' => 'admin/leaderboard', 'middleware' => [
    'auth:admin',
    'setlang',
    'can:manage-leaderboard',
], 'as' => 'admin.'], function () {
    Route::get('/', [LeaderboardController::class, 'index'])->name('leaderboard.all');
    Route::post('/generate', [LeaderboardController::class, 'generateCandidates'])->name('leaderboard.generate');
    Route::post('/approve', [LeaderboardController::class, 'approve'])->name('leaderboard.approve');
    Route::get('/remove/{user_id}', [LeaderboardController::class, 'remove'])->name('leaderboard.remove');
    Route::post('/bulk-actions', [LeaderboardController::class, 'bulk_actions'])->name('leaderboard.bulk_actions');
});

// Frontend routes (For user)
Route::group(['middleware' => ['globalVariable', 'maintains_mode', 'setlang']], function () {
    Route::get('/leaderboard', function () {
        $candidates = Entry::where('is_active', true)->orderBy('position')->limit(20)->with('user')->get();
        return view('leaderboard::frontend.index', compact('candidates'));
    })->name('leaderboard.frontend.index');
});
