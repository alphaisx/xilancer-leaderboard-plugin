<?php

use Illuminate\Support\Facades\Route;
use Modules\Leaderboard\Http\Controllers\Admin\LeaderboardController;
use Modules\Leaderboard\Entities\Entry;

Route::group(['prefix' => 'admin/leaderboard', 'middleware' => [
    'auth:admin',
    'setlang',
    // 'can:manage-leaderboard',
], 'as' => 'admin.'], function () {
    Route::get('/', [LeaderboardController::class, 'index'])->name('leaderboard.all');
    Route::post('/generate', [LeaderboardController::class, 'generateCandidates'])->name('leaderboard.generate');
    Route::post('/approve', [LeaderboardController::class, 'approve'])->name('leaderboard.approve');
});

// Frontend routes
Route::group(['middleware' => ['web']], function () {
    Route::get('/leaderboard', function () {
        $entries = Entry::where('is_active', true)->orderBy('position')->limit(20)->with('user')->get();
        return view('leaderboard::frontend.index', compact('entries'));
    })->name('leaderboard.frontend.index');

    // homepage top 3 (used as an include on the homepage)
    Route::get('/_leaderboard-home-top3', function () {
        $entries = Entry::where('is_active', true)->orderBy('position')->limit(3)->with('user')->get();
        return view('leaderboard::frontend.home', compact('entries'));
    })->name('leaderboard.frontend.home.top3');
});
