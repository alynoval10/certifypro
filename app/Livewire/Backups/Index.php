<?php

namespace App\Livewire\Backups;

use App\Services\DatabaseBackupService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use RuntimeException;

class Index extends Component
{
    public function createBackup(DatabaseBackupService $backups): void
    {
        try {
            $filename = $backups->create();
            session()->flash('success', 'Backup berhasil dibuat: '.$filename);
        } catch (\Throwable $exception) {
            report($exception);
            session()->flash('error', $exception instanceof RuntimeException ? $exception->getMessage() : 'Backup gagal dibuat. Periksa log aplikasi.');
        }
    }

    public function restoreBackup(string $filename, DatabaseBackupService $backups): void
    {
        try {
            $safetyBackup = $backups->restore($filename);
            session()->flash('success', 'Database dipulihkan. Salinan database sebelum pemulihan tersimpan sebagai '.$safetyBackup.'.');
        } catch (\Throwable $exception) {
            report($exception);
            session()->flash('error', $exception instanceof RuntimeException ? $exception->getMessage() : 'Pemulihan gagal. Periksa log aplikasi.');
        }
    }

    public function downloadBackup(string $filename, DatabaseBackupService $backups)
    {
        try {
            $path = $backups->pathFor($filename);
            abort_unless(is_file($path), 404);

            return Storage::disk('local')->download('backups/database/'.$filename);
        } catch (RuntimeException) {
            abort(404);
        }
    }

    public function render(DatabaseBackupService $backups): View
    {
        return view('livewire.backups.index', [
            'backups' => $backups->all(),
            'databaseDriver' => config('database.default'),
        ])->layout('layouts.app', ['title' => 'Backup data', 'breadcrumb' => 'Backup data']);
    }
}
