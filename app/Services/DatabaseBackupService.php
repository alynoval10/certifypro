<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\Process\Process;

class DatabaseBackupService
{
    private const DIRECTORY = 'backups/database';

    public function create(): string
    {
        $driver = config('database.default');
        $extension = $driver === 'sqlite' ? 'sqlite' : 'sql';
        $filename = 'certifypro-db-'.now()->format('Ymd-His').'-'.Str::lower(Str::random(4)).'.'.$extension;
        $path = $this->absolutePath($filename);
        Storage::disk('local')->makeDirectory(self::DIRECTORY);

        if ($driver === 'sqlite') {
            DB::connection('sqlite')->statement('PRAGMA wal_checkpoint(FULL)');
            $databasePath = $this->sqliteDatabasePath();
            if (! is_file($databasePath)) {
                throw new RuntimeException('File database SQLite tidak ditemukan: '.$databasePath);
            }
            if (! copy($databasePath, $path)) {
                throw new RuntimeException('Gagal menyalin file database SQLite.');
            }
        } elseif (in_array($driver, ['mysql', 'mariadb'], true)) {
            $this->runMysqlDump($path);
        } else {
            throw new RuntimeException("Backup otomatis belum mendukung database '{$driver}'.");
        }

        $this->deleteOlderThan(30);

        return $filename;
    }

    public function restore(string $filename): string
    {
        $backupPath = $this->absolutePath($filename);
        if (! is_file($backupPath) || ! preg_match('/^certifypro-db-[0-9]{8}-[0-9]{6}-[a-z0-9]{4}\.(sqlite|sql)$/', $filename)) {
            throw new RuntimeException('File backup tidak ditemukan atau namanya tidak valid.');
        }

        $driver = config('database.default');
        if ($driver === 'sqlite' && str_ends_with($filename, '.sqlite')) {
            $this->assertValidSqlite($backupPath);
            $safetyBackup = $this->create();
            DB::purge();
            if (! copy($backupPath, $this->sqliteDatabasePath())) {
                throw new RuntimeException('Pemulihan gagal menyalin database. Backup pengaman dibuat: '.$safetyBackup);
            }
            DB::purge();

            return $safetyBackup;
        }

        if (in_array($driver, ['mysql', 'mariadb'], true) && str_ends_with($filename, '.sql')) {
            $safetyBackup = $this->create();
            $this->runMysqlRestore($backupPath);
            DB::purge();

            return $safetyBackup;
        }

        throw new RuntimeException('Jenis file backup tidak cocok dengan database aktif.');
    }

    /** @return array<int, array{name: string, size: int, modified: int}> */
    public function all(): array
    {
        $disk = Storage::disk('local');

        return collect($disk->files(self::DIRECTORY))
            ->filter(fn (string $path): bool => preg_match('/^'.preg_quote(self::DIRECTORY, '/').'\/certifypro-db-[0-9]{8}-[0-9]{6}-[a-z0-9]{4}\.(sqlite|sql)$/', $path) === 1)
            ->map(fn (string $path): array => [
                'name' => basename($path),
                'size' => $disk->size($path),
                'modified' => $disk->lastModified($path),
            ])
            ->sortByDesc('modified')
            ->values()
            ->all();
    }

    public function pathFor(string $filename): string
    {
        if (! preg_match('/^certifypro-db-[0-9]{8}-[0-9]{6}-[a-z0-9]{4}\.(sqlite|sql)$/', $filename)) {
            throw new RuntimeException('Nama file backup tidak valid.');
        }

        return $this->absolutePath($filename);
    }

    private function absolutePath(string $filename): string
    {
        return Storage::disk('local')->path(self::DIRECTORY.'/'.$filename);
    }

    private function sqliteDatabasePath(): string
    {
        $path = (string) config('database.connections.sqlite.database');
        if ($path === ':memory:') {
            throw new RuntimeException('Database SQLite in-memory tidak dapat dibackup.');
        }

        if (! str_starts_with($path, DIRECTORY_SEPARATOR) && ! preg_match('/^[A-Za-z]:[\\\\\/]/', $path)) {
            $rootRelative = base_path($path);
            $databaseRelative = database_path($path);
            $path = is_file($rootRelative) || ! is_file($databaseRelative) ? $rootRelative : $databaseRelative;
        }

        return $path;
    }

    private function runMysqlDump(string $path): void
    {
        $config = config('database.connections.'.config('database.default'));
        $command = [
            'mysqldump',
            ...$this->mysqlConnectionArguments($config),
            '--user='.(string) ($config['username'] ?? ''),
            '--single-transaction',
            '--skip-lock-tables',
            '--routines',
            '--triggers',
            '--result-file='.$path,
            (string) ($config['database'] ?? ''),
        ];
        $process = new Process($command, base_path(), ['MYSQL_PWD' => (string) ($config['password'] ?? '')]);
        $process->setTimeout(600);
        $process->run();
        if (! $process->isSuccessful()) {
            @unlink($path);
            throw new RuntimeException('Backup MySQL gagal: '.trim($process->getErrorOutput() ?: $process->getOutput()));
        }
    }

    private function runMysqlRestore(string $path): void
    {
        $config = config('database.connections.'.config('database.default'));
        $process = new Process([
            'mysql',
            ...$this->mysqlConnectionArguments($config),
            '--user='.(string) ($config['username'] ?? ''),
            (string) ($config['database'] ?? ''),
        ], base_path(), ['MYSQL_PWD' => (string) ($config['password'] ?? '')]);
        $input = fopen($path, 'rb');
        if ($input === false) {
            throw new RuntimeException('File backup tidak dapat dibaca.');
        }
        $process->setInput($input);
        $process->setTimeout(600);
        $process->run();
        fclose($input);
        if (! $process->isSuccessful()) {
            throw new RuntimeException('Pemulihan MySQL gagal: '.trim($process->getErrorOutput() ?: $process->getOutput()));
        }
    }

    /** @param array<string, mixed> $config
     *  @return list<string>
     */
    private function mysqlConnectionArguments(array $config): array
    {
        if (! empty($config['unix_socket'])) {
            return ['--socket='.(string) $config['unix_socket']];
        }

        return [
            '--host='.(string) ($config['host'] ?? '127.0.0.1'),
            '--port='.(string) ($config['port'] ?? 3306),
        ];
    }

    private function assertValidSqlite(string $path): void
    {
        try {
            $pdo = new \PDO('sqlite:'.$path);
            $result = $pdo->query('PRAGMA integrity_check')?->fetchColumn();
            if ($result !== 'ok') {
                throw new RuntimeException('Pemeriksaan integritas SQLite gagal.');
            }
        } catch (\Throwable $exception) {
            throw new RuntimeException('File backup SQLite rusak atau tidak valid.', previous: $exception);
        }
    }

    private function deleteOlderThan(int $days): void
    {
        $cutoff = now()->subDays($days)->timestamp;
        foreach ($this->all() as $backup) {
            if ($backup['modified'] < $cutoff) {
                Storage::disk('local')->delete(self::DIRECTORY.'/'.$backup['name']);
            }
        }
    }
}
