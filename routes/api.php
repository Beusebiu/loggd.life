<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DailyCheckInController;
use App\Http\Controllers\Api\GoalController;
use App\Http\Controllers\Api\HabitController;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\VisionController;
use App\Http\Controllers\Api\WeeklyReviewController;
use App\Http\Controllers\LeaderboardController;
use Illuminate\Support\Facades\Route;

// Public authentication routes (with rate limiting)
Route::middleware(['throttle:5,1'])->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Protected routes (require authentication - support both sanctum tokens and web session)
Route::middleware(['auth:sanctum,web', 'throttle:180,1'])->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Habits
    Route::apiResource('habits', HabitController::class);
    Route::patch('/habits/{id}/archive', [HabitController::class, 'archive']);
    Route::patch('/habits/{id}/reactivate', [HabitController::class, 'reactivate']);
    Route::get('/habits/{id}/checks', [HabitController::class, 'getChecks']);
    Route::get('/habits/{id}/checks/{date}', [HabitController::class, 'getChecksForDate']);
    Route::post('/habits/{id}/check', [HabitController::class, 'toggleCheck']);
    Route::put('/habits/{id}/checks/{date}/note', [HabitController::class, 'updateCheckNote']);
    Route::get('/habits/{id}/stats', [HabitController::class, 'getStats']);
    Route::get('/habit-checks', [HabitController::class, 'getAllChecks']);
    Route::delete('/habit-checks/{id}', [HabitController::class, 'deleteCheck']);

    // Journey: Vision
    Route::get('/journey/vision', [VisionController::class, 'show']);
    Route::post('/journey/vision', [VisionController::class, 'store']);
    Route::put('/journey/vision', [VisionController::class, 'update']);
    Route::post('/journey/vision/toggle-section-privacy', [VisionController::class, 'toggleSectionPrivacy']);

    // Journey: Vision Snapshots
    Route::post('/journey/vision/snapshots', [VisionController::class, 'createSnapshot']);
    Route::get('/journey/vision/snapshots', [VisionController::class, 'getSnapshots']);
    Route::get('/journey/vision/snapshots/{id}', [VisionController::class, 'getSnapshot']);

    // Journey: Goals
    Route::get('/journey/goals', [GoalController::class, 'index']);
    Route::post('/journey/goals', [GoalController::class, 'store']);
    Route::get('/journey/goals/{id}', [GoalController::class, 'show']);
    Route::put('/journey/goals/{id}', [GoalController::class, 'update']);
    Route::delete('/journey/goals/{id}', [GoalController::class, 'destroy']);
    Route::post('/journey/goals/{id}/complete', [GoalController::class, 'complete']);

    // Journey: Goal Updates (metric/evolution tracking)
    Route::post('/journey/goals/{id}/updates', [GoalController::class, 'addUpdate']);
    Route::get('/journey/goals/{id}/updates', [GoalController::class, 'getUpdates']);

    // Journey: Goal Milestones
    Route::post('/journey/goals/{goalId}/milestones', [GoalController::class, 'storeMilestone']);
    Route::put('/journey/goals/{goalId}/milestones/{milestoneId}', [GoalController::class, 'updateMilestone']);
    Route::delete('/journey/goals/{goalId}/milestones/{milestoneId}', [GoalController::class, 'destroyMilestone']);

    // Journey: Goal Tasks
    Route::post('/journey/goals/{goalId}/tasks', [GoalController::class, 'storeTask']);
    Route::put('/journey/goals/{goalId}/tasks/{taskId}', [GoalController::class, 'updateTask']);
    Route::post('/journey/goals/{goalId}/tasks/{taskId}/toggle', [GoalController::class, 'toggleTask']);
    Route::delete('/journey/goals/{goalId}/tasks/{taskId}', [GoalController::class, 'destroyTask']);

    // Journey: Goal Metrics
    Route::post('/journey/goals/{goalId}/metrics', [GoalController::class, 'storeMetric']);
    Route::put('/journey/goals/{goalId}/metrics/{metricId}', [GoalController::class, 'updateMetric']);
    Route::delete('/journey/goals/{goalId}/metrics/{metricId}', [GoalController::class, 'destroyMetric']);

    // Journey: Daily Check-ins
    Route::get('/journey/daily', [DailyCheckInController::class, 'index']);
    Route::get('/journey/daily/{date}', [DailyCheckInController::class, 'show']);
    Route::post('/journey/daily', [DailyCheckInController::class, 'store']);
    Route::put('/journey/daily/{id}', [DailyCheckInController::class, 'update']);
    Route::get('/journey/daily/active-goals', [DailyCheckInController::class, 'getActiveGoals']);

    // Journey: Weekly Reviews
    Route::get('/journey/weekly', [WeeklyReviewController::class, 'index']);
    Route::get('/journey/weekly/{weekStartDate}', [WeeklyReviewController::class, 'show']);
    Route::post('/journey/weekly', [WeeklyReviewController::class, 'store']);
    Route::put('/journey/weekly/{id}', [WeeklyReviewController::class, 'update']);

    // Settings
    Route::patch('/settings/profile', [SettingsController::class, 'updateProfile']);
    Route::patch('/settings/privacy', [SettingsController::class, 'updatePrivacy']);
    Route::patch('/settings/password', [SettingsController::class, 'updatePassword']);
    Route::patch('/settings/email', [SettingsController::class, 'updateEmail']);
    Route::patch('/settings/dark-mode', [SettingsController::class, 'updateDarkMode']);

    // Leaderboard
    Route::get('/leaderboard/all-time', [LeaderboardController::class, 'allTime']);
    Route::get('/leaderboard/weekly', [LeaderboardController::class, 'weekly']);
    Route::get('/leaderboard/my-position', [LeaderboardController::class, 'myPosition']);
    Route::get('/leaderboard/near-miss', [LeaderboardController::class, 'nearMiss']);
});
