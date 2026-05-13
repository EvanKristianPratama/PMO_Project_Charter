<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Exception;
use Illuminate\Support\Facades\Log;

class SyncService
{
    public function pullFromCloud(?callable $onProgress = null): array
    {
        $log = function ($message, $type = 'info') use ($onProgress) {
            if ($onProgress) {
                $onProgress($message, $type);
            }
        };

        $log("Memulai proses sinkronisasi...", "info");
        $results = [
            'success' => true,
            'tables_synced' => 0,
            'errors' => [],
        ];

        // Safety Check: Don't allow sync if the default DB is the exact same as the Cloud DB
        $defaultConfig = config('database.connections.' . config('database.default'));
        $cloudConfig = config('database.connections.cloud');
        
        if (($defaultConfig['host'] ?? '') === ($cloudConfig['host'] ?? '') && 
            ($defaultConfig['database'] ?? '') === ($cloudConfig['database'] ?? '')) {
            throw new Exception("Tujuan sinkronisasi sama dengan sumber (Cloud). Harap ubah default database di .env ke 'sqlite' atau 'local' terlebih dahulu.");
        }

        $log("Memverifikasi koneksi Cloud...", "info");
        // Verify remote connection
        $driver = DB::getDriverName();
        try {
            DB::connection('cloud')->getPdo();
            $log("Koneksi Cloud berhasil.", "success");
        } catch (Exception $e) {
            $log("Gagal konek ke cloud: " . $e->getMessage(), "error");
            throw new Exception("Tidak dapat terhubung ke database cloud (Master): " . $e->getMessage());
        }

        // Disable foreign key checks for safe syncing
        $log("Menonaktifkan foreign key checks...", "info");
        $this->toggleForeignKeys(false);

        try {
            $log("Mendapatkan daftar tabel dari Master...", "info");
            // Fetch list of tables from the remote cloud connection
            $tables = DB::connection('cloud')->select('SHOW TABLES');
            
            // Determine variable dynamically depending on DB name
            $dbName = config('database.connections.cloud.database', 'defaultdb');
            $key = 'Tables_in_' . $dbName;
            if (!isset($tables[0]->$key) && count($tables) > 0) {
                $key = array_keys((array)$tables[0])[0];
            }

            $tablesToIgnore = ['migrations', 'failed_jobs', 'cache', 'cache_locks', 'jobs', 'job_batches', 'sessions', 'activity_log'];
            $totalFound = count($tables);
            $log("Ditemukan total {$totalFound} tabel.", "info");

            foreach ($tables as $tableObj) {
                $table = $tableObj->$key;

                if (in_array($table, $tablesToIgnore)) {
                    continue;
                }

                $log("Sedang memproses tabel: {$table}...", "info");
                try {
                    // 1. Ensure local table structure exists
                    if (!Schema::hasTable($table)) {
                        // Get Schema from MySQL Cloud
                        $createStmtResult = DB::connection('cloud')->select("SHOW CREATE TABLE `{$table}`");
                        
                        if (empty($createStmtResult)) {
                            throw new Exception("Could not retrieve schema definition for {$table}");
                        }
                        
                        $createStmt = $createStmtResult[0]->{'Create Table'};
                        
                        // Convert Schema MySQL specifically for SQLite if the driver is sqlite
                        if ($driver === 'sqlite') {
                            // Strip MySQL params
                            $createStmt = preg_replace('/ENGINE=.*?$/i', '', $createStmt);
                            $createStmt = preg_replace('/AUTO_INCREMENT=\d+/i', '', $createStmt);
                            $createStmt = preg_replace('/DEFAULT CHARSET=[^\s]+/i', '', $createStmt);
                            $createStmt = preg_replace('/COLLATE=[^\s]+/i', '', $createStmt);
                            
                            // Auto increment
                            $createStmt = preg_replace('/(int|bigint)(\(\d+\))?(\s+unsigned)? NOT NULL AUTO_INCREMENT/i', 'INTEGER PRIMARY KEY AUTOINCREMENT', $createStmt);
                            
                            // Specific timestamp compatibilities
                            $createStmt = str_ireplace('ON UPDATE CURRENT_TIMESTAMP', '', $createStmt);
                            
                            // Remove heavy non-sqlite indexes and constraint lines temporarily
                            $createStmt = preg_replace('/,\s*(PRIMARY KEY|KEY|UNIQUE KEY|CONSTRAINT).*/s', "\n)", $createStmt);
                            $createStmt = preg_replace('/,\s*\)/', "\n)", $createStmt);
                        }

                        // Attempt creation
                        try {
                            DB::statement($createStmt);
                        } catch (Exception $exCreate) {
                            // Fallback: Create minimalistic table with just string columns for data backup
                            Log::warning("Failed fast SQL convert for {$table}. Falling back to lazy generic build.");
                            $columns = Schema::connection('cloud')->getColumnListing($table);
                            Schema::create($table, function($blueprint) use ($columns) {
                                foreach ($columns as $col) {
                                    if ($col === 'id') $blueprint->id();
                                    else $blueprint->text($col)->nullable();
                                }
                            });
                        }
                    } else {
                        // 1.b Ensure local table has all columns from Cloud
                        $cloudColumns = Schema::connection('cloud')->getColumnListing($table);
                        $localColumns = Schema::getColumnListing($table);
                        $missingColumns = array_diff($cloudColumns, $localColumns);

                        if (!empty($missingColumns)) {
                            $log("Menambahkan kolom baru ke tabel {$table}: " . implode(', ', $missingColumns), "info");
                            Schema::table($table, function (Blueprint $blueprint) use ($missingColumns) {
                                foreach ($missingColumns as $column) {
                                    $blueprint->text($column)->nullable();
                                }
                            });
                        }
                    }

                    // 2. Truncate/Clear local table data
                    DB::table($table)->delete();

                    // 2. Retrieve from Cloud
                    $rows = DB::connection('cloud')->table($table)->get();
                    $insertData = [];
                    
                    foreach ($rows as $row) {
                        $insertData[] = (array) $row;
                    }

                    if (count($insertData) > 0) {
                        // Insert in chunks to prevent large query packet limits
                        $count = count($insertData);
                        $log("- Memasukkan {$count} baris data...", "info");
                        foreach (array_chunk($insertData, 250) as $chunk) {
                            DB::table($table)->insert($chunk);
                        }
                    }

                    $results['tables_synced']++;
                    $log("✓ Tabel '{$table}' selesai.", "success");
                } catch (Exception $tableEx) {
                    $results['errors'][] = "Gagal menyinkronkan tabel '{$table}': " . $tableEx->getMessage();
                    $log("✘ Tabel '{$table}' GAGAL: " . $tableEx->getMessage(), "error");
                    Log::error("Sync table failed: {$table}", ['exception' => $tableEx]);
                }
            }

        } catch (Exception $globalEx) {
            $results['success'] = false;
            $results['errors'][] = "Kegagalan sistem saat sinkronisasi: " . $globalEx->getMessage();
            $log("GAGAL GLOBAL: " . $globalEx->getMessage(), "error");
        } finally {
            // Re-enable foreign key checks
            $log("Mengaktifkan kembali foreign key checks...", "info");
            $this->toggleForeignKeys(true);
            $log("Proses selesai.", "info");
        }

        return $results;
    }

    private function toggleForeignKeys(bool $enable): void
    {
        $driver = DB::getDriverName();
        
        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ' . ($enable ? 'ON' : 'OFF'));
        } elseif ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement('SET FOREIGN_KEY_CHECKS=' . ($enable ? '1' : '0'));
        }
    }
}
