<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SyncService;

class PullCloudCommand extends Command
{
    protected $signature = 'app:pull-cloud';
    protected $description = 'Pull full dataset from Cloud Master to Local DB via command line';

    public function handle(SyncService $syncService)
    {
        $this->info('Memulai penarikan data dari Cloud...');
        
        try {
            $result = $syncService->pullFromCloud();
            
            if (count($result['errors']) > 0) {
                $this->warn("Sinkronisasi selesai dengan peringatan:");
                foreach ($result['errors'] as $error) {
                    $this->error(" - {$error}");
                }
            } else {
                $this->info("Sukses! Total {$result['tables_synced']} tabel berhasil disinkronkan.");
            }
        } catch (\Exception $e) {
            $this->error("Gagal menjalankan perintah: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
