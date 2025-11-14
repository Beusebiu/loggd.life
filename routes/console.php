<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Leaderboard scheduled tasks
Schedule::command('leaderboard:reset-weekly')
    ->weekly()
    ->mondays()
    ->at('00:00')
    ->timezone('UTC');

Schedule::command('leaderboard:send-sunday-reminders')
    ->sundays()
    ->at('20:00') // 8pm
    ->timezone('UTC');

Schedule::command('leaderboard:send-sunday-reminders')
    ->sundays()
    ->at('22:00') // 10pm
    ->timezone('UTC');

Schedule::command('leaderboard:send-sunday-reminders')
    ->sundays()
    ->at('23:30') // 11:30pm
    ->timezone('UTC');
