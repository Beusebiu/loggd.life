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
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:5,1');

    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])
        ->middleware('throttle:5,1');

    // Password reset routes
    Route::post('/forgot-password', [App\Http\Controllers\Auth\PasswordResetLinkController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('password.email');

    Route::post('/reset-password', [App\Http\Controllers\Auth\NewPasswordController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('password.store');
});

// Email verification routes
Route::middleware(['auth'])->group(function () {
    Route::get('/verify-email', function () {
        return Inertia::render('Auth/VerifyEmail');
    })->name('verification.notice');

    Route::get('/verify-email/{id}/{hash}', [App\Http\Controllers\Auth\VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('message', 'Verification link sent!');
    })->middleware('throttle:6,1')->name('verification.send');
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
    return Inertia::render('Leaderboard');
})->middleware('auth')->name('profile.leaderboard');
Route::get('/@{username}/settings', function () {
    return Inertia::render('Settings');
})->middleware('auth')->name('profile.settings');
