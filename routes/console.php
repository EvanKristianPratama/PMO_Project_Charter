<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('backup:run --only-db')
    ->timezone('Asia/Jakarta')
    ->dailyAt('17:00')
    ->withoutOverlapping();

Schedule::command('backup:clean')
    ->timezone('Asia/Jakarta')
    ->dailyAt('17:30')
    ->withoutOverlapping();
