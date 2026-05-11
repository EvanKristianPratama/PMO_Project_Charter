<?php

namespace App\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Spatie\Backup\BackupDestination\Backup;
use Spatie\Backup\BackupDestination\BackupDestination;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupService
{
    public function paginateBackups(int $perPage = 10): LengthAwarePaginator
    {
        $page = Paginator::resolveCurrentPage('page');
        $items = $this->listBackups();

        return new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ],
        );
    }

    public function listBackups(): Collection
    {
        return $this->backupDestination()
            ->backups()
            ->map(fn (Backup $backup): array => $this->transformBackup($backup))
            ->values();
    }

    public function getStats(): array
    {
        $backups = $this->listBackups();
        $latestBackup = $backups->first();

        return [
            'total_backups' => $backups->count(),
            'total_size' => $this->formatBytes((float) $backups->sum('size_bytes')),
            'retention_days' => $this->getRetentionDays(),
            'latest_backup_at' => $latestBackup['created_at'] ?? null,
            'storage_path' => storage_path('app'.DIRECTORY_SEPARATOR.'private'.DIRECTORY_SEPARATOR.$this->backupName()),
        ];
    }

    public function getRetentionDays(): int
    {
        $retentionDays = $this->readSettings()['retention_days'] ?? $this->defaultRetentionDays();

        return max(1, (int) $retentionDays);
    }

    public function updateRetentionDays(int $retentionDays): int
    {
        $settings = $this->readSettings();
        $settings['retention_days'] = $retentionDays;

        $settingsPath = $this->settingsPath();

        File::ensureDirectoryExists(dirname($settingsPath));

        if (File::put($settingsPath, json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) === false) {
            throw new RuntimeException('Gagal menyimpan pengaturan backup.');
        }

        return $retentionDays;
    }

    public function runDatabaseBackup(): void
    {
        $lock = Cache::lock('backup:database:run', 900);

        if (! $lock->get()) {
            throw new RuntimeException('Backup database sedang berjalan. Silakan coba lagi beberapa saat.');
        }

        try {
            $exitCode = Artisan::call('backup:database-run');

            if ($exitCode !== 0) {
                $output = trim(Artisan::output());

                Log::error('Manual database backup failed.', [
                    'output' => $output,
                ]);

                throw new RuntimeException($this->formatBackupFailureMessage($output));
            }
        } finally {
            $lock->release();
        }
    }

    public function downloadBackup(string $fileName): StreamedResponse
    {
        $this->ensureSafeFileName($fileName);

        $backup = $this->listBackups()
            ->first(fn (array $item): bool => $item['file_name'] === $fileName);

        if ($backup === null) {
            throw new RuntimeException('File backup tidak ditemukan.');
        }

        return Storage::disk($this->diskName())->download(
            $backup['path'],
            $backup['file_name'],
            ['Content-Type' => 'application/zip'],
        );
    }

    private function transformBackup(Backup $backup): array
    {
        $timestamp = $backup->date();
        $fileName = basename($backup->path());
        $sizeInBytes = (float) $backup->sizeInBytes();

        return [
            'file_name' => $fileName,
            'path' => $backup->path(),
            'date' => $timestamp->toDateString(),
            'time' => $timestamp->format('H:i:s'),
            'created_at' => $timestamp->toIso8601String(),
            'size' => $this->formatBytes($sizeInBytes),
            'size_bytes' => $sizeInBytes,
        ];
    }

    private function backupDestination(): BackupDestination
    {
        return BackupDestination::create($this->diskName(), $this->backupName())->fresh();
    }

    private function diskName(): string
    {
        return (string) (Arr::first(config('backup.backup.destination.disks', [])) ?? 'backup');
    }

    private function backupName(): string
    {
        return (string) config('backup.backup.name', config('app.name', 'laravel-backup'));
    }

    private function defaultRetentionDays(): int
    {
        return (int) config('backup.retention_days', 14);
    }

    private function settingsPath(): string
    {
        return storage_path('app'.DIRECTORY_SEPARATOR.'settings'.DIRECTORY_SEPARATOR.'backup.json');
    }

    private function readSettings(): array
    {
        $settingsPath = $this->settingsPath();

        if (! File::exists($settingsPath)) {
            return [];
        }

        $decoded = json_decode(File::get($settingsPath), true);

        return is_array($decoded) ? $decoded : [];
    }

    private function ensureSafeFileName(string $fileName): void
    {
        if (
            $fileName === ''
            || basename($fileName) !== $fileName
            || str_contains($fileName, '/')
            || str_contains($fileName, '\\')
            || ! preg_match('/\A[a-zA-Z0-9._-]+\.zip\z/', $fileName)
        ) {
            throw new RuntimeException('Nama file backup tidak valid.');
        }
    }

    private function formatBytes(float $bytes): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = min((int) floor(log($bytes, 1024)), count($units) - 1);
        $value = $bytes / (1024 ** $power);

        return number_format($value, $power === 0 ? 0 : 2).' '.$units[$power];
    }

    private function formatBackupFailureMessage(string $output): string
    {
        $normalizedOutput = trim((string) preg_replace('/\s+/', ' ', $output));

        if ($normalizedOutput === '') {
            return 'Gagal menjalankan backup database.';
        }

        if (
            str_contains($normalizedOutput, 'mysqldump')
            && str_contains($normalizedOutput, 'not recognized as an internal or external command')
        ) {
            return 'Backup database gagal karena mysqldump tidak ditemukan di server. Atur DB_DUMP_BINARY_PATH atau pastikan MySQL client terpasang.';
        }

        if (
            str_contains($normalizedOutput, 'mariadb-dump')
            || (
                str_contains($normalizedOutput, 'mysqldump')
                && str_contains($normalizedOutput, 'set-gtid-purged')
            )
        ) {
            return 'Backup database gagal karena proses dump di container berjalan dengan client MariaDB tetapi masih membawa opsi MySQL `set-gtid-purged`. Untuk environment Docker, arahkan backup ke connection MariaDB dengan `DB_BACKUP_CONNECTION=mariadb`, lalu jalankan `php artisan config:clear`. Jika tetap harus memakai connection `mysql`, kosongkan `DB_DUMP_MYSQL_GTID_PURGED`.';
        }

        if ($this->isWindowsDumpSocketError($normalizedOutput)) {
            return 'Backup database gagal karena `mysqldump.exe` di Windows tidak bisa membuka koneksi TCP ke server database. Jika database berada di server remote, izinkan `mysqldump.exe` lewat firewall/antivirus, pastikan host dan port database dapat diakses dari mesin aplikasi, lalu coba isi `DB_DUMP_HOST` dengan hostname database, set `DB_DUMP_RESOLVE_HOST=false`, dan jalankan `php artisan optimize:clear`. Jika koneksi database memakai SSL, pastikan file CA pada `MYSQL_ATTR_SSL_CA` atau `DB_DUMP_EXTRA_OPTION` masih ada dan bisa dibaca proses backup.';
        }

        if (str_contains($normalizedOutput, 'Unknown MySQL server host')) {
            return 'Backup database gagal karena host database tidak bisa di-resolve oleh proses dump. Pastikan DB_HOST benar atau isi DB_DUMP_HOST dengan IP server database.';
        }

        if (str_contains($normalizedOutput, 'exceeded the timeout of')) {
            return 'Backup database gagal karena proses dump melebihi batas waktu. Naikkan DB_DUMP_TIMEOUT atau kurangi beban query saat backup berjalan.';
        }

        $summary = preg_split('/\s+#0\s+/', $normalizedOutput)[0] ?? $normalizedOutput;

        return $summary !== '' ? $summary : 'Gagal menjalankan backup database.';
    }

    private function isWindowsDumpSocketError(string $normalizedOutput): bool
    {
        if (! str_contains($normalizedOutput, 'mysqldump')) {
            return false;
        }

        $indicators = [
            'Got error: 2003',
            'Got error: 2004',
            "Can't connect to MySQL server",
            "Can't create TCP/IP socket",
            '(10013)',
            '(10106)',
            'An attempt was made to access a socket in a way forbidden by its access permissions',
            'Service provider failed to initialize',
        ];

        foreach ($indicators as $indicator) {
            if (str_contains($normalizedOutput, $indicator)) {
                return true;
            }
        }

        return false;
    }
}
