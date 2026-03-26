<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BackupRetentionUpdateRequest;
use App\Services\BackupService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupController extends Controller
{
    public function __construct(
        private readonly BackupService $backupService,
    ) {}

    public function index(): Response
    {
        return Inertia::render('Admin/Backup/Index', [
            'backups' => $this->backupService->paginateBackups(),
            'stats' => $this->backupService->getStats(),
            'settings' => [
                'retention_days' => $this->backupService->getRetentionDays(),
            ],
        ]);
    }

    public function download(string $file): StreamedResponse|RedirectResponse
    {
        try {
            return $this->backupService->downloadBackup($file);
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    public function updateRetention(BackupRetentionUpdateRequest $request): RedirectResponse
    {
        try {
            $retentionDays = $this->backupService->updateRetentionDays(
                (int) $request->validated('retention_days'),
            );

            return back()->with('success', "Retensi backup berhasil diatur menjadi {$retentionDays} hari.");
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }
}
