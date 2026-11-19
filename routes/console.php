<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Services\DatabaseBackupService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:backup-database', function (DatabaseBackupService $backups): void {
    $filename = $backups->create();
    $this->info('Database backup berhasil dibuat: '.$filename);
})->purpose('Create a private database backup');

Schedule::command('app:backup-database')->dailyAt('02:00')->withoutOverlapping();
