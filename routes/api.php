<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HabitController;
use App\Http\Controllers\Api\EntryController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CheckInController;
use App\Http\Controllers\Api\TimelineController;
use App\Http\Controllers\Api\StatsController;
use App\Http\Controllers\Api\SettingsController;
use Illuminate\Support\Facades\Route;

// Public authentication routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (require authentication - support both sanctum tokens and web session)
Route::middleware(['auth:sanctum,web'])->group(function () {
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

    // Entries (daily logging)
    Route::apiResource('entries', EntryController::class);
    Route::patch('/entries/{id}/toggle-public', [EntryController::class, 'togglePublic']);

    // Categories
    Route::apiResource('categories', CategoryController::class);

    // Templates (for check-ins)
    Route::get('/templates', [CheckInController::class, 'getTemplates']);
    Route::get('/templates/{id}', [CheckInController::class, 'getTemplate']);
    Route::get('/templates/{templateId}/check-ins', [CheckInController::class, 'getCheckInsByTemplate']);
    Route::post('/templates/{templateId}/check-ins', [CheckInController::class, 'storeWithTemplate']);
    Route::get('/templates/{templateId}/versions', [CheckInController::class, 'getVersions']);

    // Template Management
    Route::get('/templates-manage', [\App\Http\Controllers\Api\TemplateController::class, 'index']);
    Route::post('/templates-manage', [\App\Http\Controllers\Api\TemplateController::class, 'store']);
    Route::put('/templates-manage/{id}', [\App\Http\Controllers\Api\TemplateController::class, 'update']);
    Route::delete('/templates-manage/{id}', [\App\Http\Controllers\Api\TemplateController::class, 'destroy']);
    Route::patch('/templates-manage/{id}/toggle-visibility', [\App\Http\Controllers\Api\TemplateController::class, 'toggleVisibility']);
    Route::patch('/templates-manage/{id}/reorder', [\App\Http\Controllers\Api\TemplateController::class, 'reorder']);

    // Check-ins (daily/weekly/monthly reviews)
    Route::get('/check-ins/dashboard', [CheckInController::class, 'dashboard']);
    Route::apiResource('check-ins', CheckInController::class);
    Route::put('/check-ins/{id}', [CheckInController::class, 'updateCheckIn']);
    Route::get('/check-ins/template/{type}', [CheckInController::class, 'getTemplateLegacy']);

    // Timeline (unified activity feed)
    Route::get('/timeline', [TimelineController::class, 'index']);
    Route::get('/timeline/stats', [TimelineController::class, 'stats']);

    // Stats (analytics and insights)
    Route::get('/stats', [StatsController::class, 'index']);

    // Settings
    Route::patch('/settings/profile', [SettingsController::class, 'updateProfile']);
    Route::patch('/settings/privacy', [SettingsController::class, 'updatePrivacy']);
    Route::patch('/settings/password', [SettingsController::class, 'updatePassword']);
    Route::patch('/settings/email', [SettingsController::class, 'updateEmail']);
});
