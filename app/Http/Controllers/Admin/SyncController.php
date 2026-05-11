<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class SyncController extends Controller
{
    public function __construct(
        private readonly SyncService $syncService,
    ) {}

    public function index(): Response
    {
        // Retrieve some basic stats to show on sync screen
        $localDriver = DB::getDriverName();
        $cloudConfig = config('database.connections.cloud');
        
        return Inertia::render('Sync/Index', [
            'stats' => [
                'local_driver' => $localDriver,
                'cloud_host' => $cloudConfig['host'] ?? 'N/A',
                'is_cloud_accessible' => $this->checkCloudConnection(),
            ]
        ]);
    }

    public function pull(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        return response()->stream(function () {
            // Disconnect logic output buffering so data flows directly
            if (ob_get_level()) ob_end_clean();

            $send = function (array $data) {
                echo json_encode($data) . "\n";
                flush();
            };

            try {
                $result = $this->syncService->pullFromCloud(function ($message, $type) use ($send) {
                    $send([
                        'event' => 'progress',
                        'type' => $type,
                        'message' => $message,
                        'time' => now()->format('H:i:s')
                    ]);
                });

                $send([
                    'event' => 'complete',
                    'success' => count($result['errors']) === 0,
                    'message' => count($result['errors']) === 0 
                        ? "Berhasil! Seluruh data ({$result['tables_synced']} tabel) telah disinkronkan." 
                        : "Sinkronisasi sebagian selesai. Ada " . count($result['errors']) . " error."
                ]);

            } catch (\Exception $e) {
                $send([
                    'event' => 'error',
                    'message' => 'Gagal melakukan pull data: ' . $e->getMessage()
                ]);
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'X-Accel-Buffering' => 'no', // For Nginx proxies
            'Cache-Control' => 'no-cache',
        ]);
    }

    private function checkCloudConnection(): bool
    {
        try {
            DB::connection('cloud')->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
