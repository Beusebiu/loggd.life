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
        return redirect('/@' . Auth::user()->username);
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
Route::get('/@{username}/entries', [ProfileController::class, 'entries'])->name('profile.entries');
Route::get('/@{username}/check-ins', function () {
    return Inertia::render('CheckIns');
})->middleware('auth')->name('profile.checkins');
Route::get('/@{username}/timeline', function () {
    return Inertia::render('Timeline');
})->middleware('auth')->name('profile.timeline');
Route::get('/@{username}/stats', function () {
    return Inertia::render('Stats');
})->middleware('auth')->name('profile.stats');
Route::get('/@{username}/settings', function () {
    return Inertia::render('Settings');
})->middleware('auth')->name('profile.settings');
