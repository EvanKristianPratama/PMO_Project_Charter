<?php

namespace App\Backup\Cleanup;

use App\Services\BackupService;
use Spatie\Backup\BackupDestination\Backup;
use Spatie\Backup\BackupDestination\BackupCollection;
use Spatie\Backup\Config\Config;
use Spatie\Backup\Tasks\Cleanup\CleanupStrategy;

class RetentionDaysStrategy extends CleanupStrategy
{
    public function __construct(
        Config $config,
        private readonly BackupService $backupService,
    ) {
        parent::__construct($config);
    }

    public function deleteOldBackups(BackupCollection $backups): void
    {
        $cutoffDate = now()->subDays($this->backupService->getRetentionDays());

        $backups
            ->filter(fn (Backup $backup): bool => $backup->exists() && $backup->date()->lt($cutoffDate))
            ->each(fn (Backup $backup): null => $backup->delete());
    }
}
