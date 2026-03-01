<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|─────────────────────────────────────────────────────────────────────────────
| AI Analytics Scheduling — Laravel 11
| (Replaces the old Console/Kernel.php which is not used in Laravel 11)
|─────────────────────────────────────────────────────────────────────────────
*/

// Update customer + product analytics every hour
Schedule::command('analytics:update')->hourly()
    ->withoutOverlapping()
    ->runInBackground();

// Full AI pattern scan every 6 hours
Schedule::command('analytics:detect-patterns')->everySixHours()
    ->withoutOverlapping()
    ->runInBackground();

// Generate 7-day sales predictions daily at midnight
Schedule::command('analytics:predict --days=7 --period=day')
    ->dailyAt('00:00')
    ->withoutOverlapping();

// Generate weekly predictions every Monday
Schedule::command('analytics:predict --days=4 --period=week')
    ->weeklyOn(1, '00:30');

// Generate monthly prediction on 1st of each month
Schedule::command('analytics:predict --days=3 --period=month')
    ->monthlyOn(1, '01:00');