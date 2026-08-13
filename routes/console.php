<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// It checks if any of the projects meet the criteria within a weekly basis.
Schedule::command('mail:send-reminders')->weeklyOn(1, '09:00');
Schedule::command('mail:send-warnings')->weeklyOn(3, '09:00');
Schedule::command('projects:auto-archive')->dailyAt('00:00')->withoutOverlapping();
