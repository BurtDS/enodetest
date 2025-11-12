<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule vehicle data synchronization
Schedule::command('vehicles:sync')
    ->hourly()
    ->withoutOverlapping()
    ->onOneServer();

// Schedule vehicle data refresh
Schedule::command('vehicles:refresh --all')
    ->everyThirtyMinutes()
    ->withoutOverlapping()
    ->onOneServer();
