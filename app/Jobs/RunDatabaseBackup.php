<?php

namespace App\Jobs;

use App\Exceptions\BackupAlreadyRunningException;
use App\Services\BackupService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class RunDatabaseBackup implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public int $timeout;

    public bool $failOnTimeout = true;

    public function __construct(
        public readonly ?string $dispatchLockOwner = null,
    ) {
        $this->timeout = max(900, (int) env('DB_QUEUE_RETRY_AFTER', 2100) - 60);
    }

    public function handle(BackupService $backupService): void
    {
        $shouldReleaseDispatchLock = true;

        try {
            $backupService->runDatabaseBackup();
        } catch (BackupAlreadyRunningException) {
            $shouldReleaseDispatchLock = false;

            $this->release(60);

            return;
        } finally {
            if ($shouldReleaseDispatchLock) {
                $backupService->releaseQueuedBackupLock($this->dispatchLockOwner);
            }
        }
    }

    public function failed(Throwable $exception): void
    {
        app(BackupService::class)->releaseQueuedBackupLock($this->dispatchLockOwner);
    }
}
