<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Landing page
Route::get('/', function () {
    if (Auth::check()) {
        // Redirect to user's profile
        return redirect('/@'.Auth::user()->username);
    }

    return Inertia::render('Landing');
})->name('home');

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// User profile routes (public or private based on settings)
Route::get('/@{username}', [ProfileController::class, 'show'])->name('profile');
Route::get('/@{username}/habits', [ProfileController::class, 'habits'])->middleware('auth')->name('profile.habits');
Route::get('/@{username}/journey', function () {
    return Inertia::render('Journey');
})->middleware('auth')->name('profile.journey');
Route::get('/@{username}/journey/goals', function () {
    return Inertia::render('Goals');
})->middleware('auth')->name('profile.journey.goals');
Route::get('/@{username}/journey/daily', function () {
    return Inertia::render('Daily');
})->middleware('auth')->name('profile.journey.daily');
Route::get('/@{username}/journey/weekly', function () {
    return Inertia::render('Weekly');
})->middleware('auth')->name('profile.journey.weekly');
Route::get('/@{username}/leaderboard', function () {
    $users = \App\Models\User::where('profile_public', true)
        ->orderBy('total_points', 'desc')
        ->limit(100)
        ->get(['id', 'name', 'username', 'total_points', 'current_level', 'current_streak', 'longest_streak']);

    return Inertia::render('Leaderboard', [
        'users' => $users,
    ]);
})->middleware('auth')->name('profile.leaderboard');
Route::get('/@{username}/settings', function () {
    return Inertia::render('Settings');
})->middleware('auth')->name('profile.settings');
