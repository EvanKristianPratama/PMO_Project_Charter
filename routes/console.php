<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('backup:database-run', function () {
    $connectionName = collect(config('backup.backup.source.databases', []))
        ->first(fn ($connection): bool => is_string($connection) && $connection !== '')
        ?? config('database.default');

    if (is_string($connectionName) && $connectionName !== '') {
        $connectionConfig = config("database.connections.{$connectionName}");
        $driver = strtolower((string) ($connectionConfig['driver'] ?? ''));
        $explicitDumpHost = env('DB_DUMP_HOST');
        $hasExplicitDumpHost = is_string($explicitDumpHost) && trim($explicitDumpHost) !== '';
        $shouldResolveDumpHost = filter_var(
            env('DB_DUMP_RESOLVE_HOST', PHP_OS_FAMILY === 'Windows'),
            FILTER_VALIDATE_BOOL,
        );

        if (is_array($connectionConfig) && in_array($driver, ['mysql', 'mariadb'], true)) {
            $dumpHost = $hasExplicitDumpHost
                ? trim($explicitDumpHost)
                : trim((string) ($connectionConfig['dump']['host'] ?? $connectionConfig['host'] ?? ''));

            if ($dumpHost !== '') {
                if (! $hasExplicitDumpHost && $shouldResolveDumpHost && filter_var($dumpHost, FILTER_VALIDATE_IP) === false) {
                    $resolvedDumpHost = gethostbyname($dumpHost);

                    if ($resolvedDumpHost !== $dumpHost && filter_var($resolvedDumpHost, FILTER_VALIDATE_IP) !== false) {
                        $dumpHost = $resolvedDumpHost;
                    }
                }

                config(["database.connections.{$connectionName}.dump.host" => $dumpHost]);
            }
        }
    }

    return $this->call('backup:run', [
        '--only-db' => true,
    ]);
})->purpose('Run a database-only backup with runtime dump host resolution.');

Schedule::command('backup:database-run')
    ->timezone('Asia/Jakarta')
    ->dailyAt('17:00')
    ->withoutOverlapping();

Schedule::command('backup:clean')
    ->timezone('Asia/Jakarta')
    ->dailyAt('17:30')
    ->withoutOverlapping();
