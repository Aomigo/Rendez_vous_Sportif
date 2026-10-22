<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('events:close-started')
    ->everyMinute()
    ->withoutOverlapping();

Schedule::command('events:send-daily-reports')
    ->dailyAt('08:00')
    ->timezone(config('app.timezone', 'Europe/Paris'));
