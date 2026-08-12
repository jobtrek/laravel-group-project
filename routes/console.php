<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// The friendly reminder tracks a month of silence, so a weekly sweep is enough.
Schedule::command('mail:send-reminders')->weeklyOn(1, '09:00');
// The escalation must land one week after the reminder, not on the next weekday
// slot; it is safe to run daily because escalated_at makes it fire only once.
Schedule::command('mail:send-warnings')->dailyAt('09:00');
Schedule::command('projects:auto-archive')->dailyAt('00:00')->withoutOverlapping();
