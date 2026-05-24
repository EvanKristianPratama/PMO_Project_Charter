<?php

namespace App\Services;

use Exception;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

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

        $defaultConfig = config('database.connections.' . config('database.default'));
        $cloudConfig = config('database.connections.cloud');

        if (($defaultConfig['host'] ?? '') === ($cloudConfig['host'] ?? '') &&
            ($defaultConfig['database'] ?? '') === ($cloudConfig['database'] ?? '')) {
            throw new Exception("Tujuan sinkronisasi sama dengan sumber (Cloud). Harap ubah default database di .env ke 'sqlite' atau 'local' terlebih dahulu.");
        }

        $log("Memverifikasi koneksi Cloud...", "info");
        $driver = DB::getDriverName();
        $isSqliteTarget = $driver === 'sqlite';

        try {
            DB::connection('cloud')->getPdo();
            $log("Koneksi Cloud berhasil.", "success");
        } catch (Exception $e) {
            $log("Gagal konek ke cloud: " . $e->getMessage(), "error");
            throw new Exception("Tidak dapat terhubung ke database cloud (Master): " . $e->getMessage());
        }

        $log("Menonaktifkan foreign key checks...", "info");
        $this->toggleForeignKeys(false);

        try {
            $log("Mendapatkan daftar tabel dari Master...", "info");
            $tables = DB::connection('cloud')->select('SHOW TABLES');

            $dbName = config('database.connections.cloud.database', 'defaultdb');
            $key = 'Tables_in_' . $dbName;
            if (!isset($tables[0]->$key) && count($tables) > 0) {
                $key = array_keys((array) $tables[0])[0];
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
                    $createStmtResult = DB::connection('cloud')->select("SHOW CREATE TABLE `{$table}`");

                    if (empty($createStmtResult)) {
                        throw new Exception("Could not retrieve schema definition for {$table}");
                    }

                    $createStmt = $createStmtResult[0]->{'Create Table'};

                    if ($isSqliteTarget) {
                        if (Schema::hasTable($table)) {
                            Schema::dropIfExists($table);
                        }

                        $cloudColumns = DB::connection('cloud')->select("SHOW FULL COLUMNS FROM `{$table}`");
                        try {
                            Schema::create($table, function (Blueprint $blueprint) use ($cloudColumns) {
                                foreach ($cloudColumns as $column) {
                                    $columnName = $column->Field;
                                    $columnType = strtolower((string) $column->Type);

                                    if ($columnName === 'id') {
                                        $blueprint->id($columnName);
                                        continue;
                                    }

                                    if (str_contains($columnType, 'bigint')) {
                                        $blueprint->bigInteger($columnName)->nullable();
                                    } elseif (str_contains($columnType, 'int')) {
                                        $blueprint->integer($columnName)->nullable();
                                    } elseif (str_contains($columnType, 'decimal') || str_contains($columnType, 'numeric')) {
                                        $blueprint->decimal($columnName, 20, 6)->nullable();
                                    } elseif (str_contains($columnType, 'float') || str_contains($columnType, 'double')) {
                                        $blueprint->float($columnName)->nullable();
                                    } elseif (str_contains($columnType, 'date') || str_contains($columnType, 'time')) {
                                        $blueprint->dateTime($columnName)->nullable();
                                    } elseif (str_contains($columnType, 'json')) {
                                        $blueprint->json($columnName)->nullable();
                                    } else {
                                        $blueprint->text($columnName)->nullable();
                                    }
                                }
                            });
                        } catch (Exception $exCreate) {
                            Log::warning("Failed to rebuild SQLite schema for {$table}. Falling back to generic build.", [
                                'exception' => $exCreate,
                            ]);

                            $columns = DB::connection('cloud')->getSchemaBuilder()->getColumnListing($table);
                            Schema::create($table, function ($blueprint) use ($columns) {
                                foreach ($columns as $col) {
                                    if ($col === 'id') {
                                        $blueprint->id();
                                        continue;
                                    }

                                    $blueprint->text($col)->nullable();
                                }
                            });
                        }
                    } elseif (!Schema::hasTable($table)) {
                        DB::statement($createStmt);
                    } else {
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

                    DB::table($table)->delete();

                    $rows = DB::connection('cloud')->table($table)->get();
                    $insertData = [];

                    foreach ($rows as $row) {
                        $insertData[] = (array) $row;
                    }

                    if (count($insertData) > 0) {
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
