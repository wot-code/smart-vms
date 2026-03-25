<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Data Protection Act (DPA) Compliance: 
// Schedule automatic deletion of old database records daily
use Illuminate\Support\Facades\Schedule;

Schedule::command('model:prune', [
    '--model' => [App\Models\Visitor::class],
])->dailyAt('00:00');
