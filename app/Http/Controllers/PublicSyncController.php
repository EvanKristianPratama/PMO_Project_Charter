<?php

namespace App\Http\Controllers;

use App\Services\SyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class PublicSyncController extends Controller
{
    public function __construct(
        private readonly SyncService $syncService
    ) {}

    public function sync(): JsonResponse
    {
        try {
            // Temporarily increase timeout for execution
            set_time_limit(300);
            
            // Force target destination database specifically to 'sqlite' for Public Desktop Sync, 
            // regardless of active .env or session setting.
            config(['database.default' => 'sqlite']);
            \Illuminate\Support\Facades\DB::purge(); 

            $result = $this->syncService->pullFromCloud();
            
            $successCount = count($result['errors'] ?? []) === 0;
            
            return response()->json([
                'success' => $successCount,
                'message' => $successCount 
                    ? 'Data berhasil diperbarui dari Master.'
                    : 'Sebagian data gagal diperbarui.',
                'errors' => $result['errors'] ?? []
            ]);
            
        } catch (\Exception $e) {
            Log::error("Public Sync Failed: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan pembaruan data: ' . $e->getMessage()
            ], 500);
        }
    }
}
