<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

\Illuminate\Support\Facades\Schedule::command('invoice:over-due')
    ->dailyAt('01:00') // Adjust the time as needed
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('Failed to run invoice:over-due command');
    })
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('Successfully ran invoice:over-due command');
    });

\Illuminate\Support\Facades\Schedule::command('invoice:cancalled')
    ->dailyAt('02:00') // Adjust the time as needed
    //->everyMinute() // For testing purposes, you can use everyMinute
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('Failed to run invoice:cancelled command');
    })
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('Successfully ran invoice:cancelled command');
    });