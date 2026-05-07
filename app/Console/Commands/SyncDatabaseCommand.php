<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SyncDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-to-sqlite';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all data from Aiven MySQL to local NativePHP SQLite database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Mempersiapkan sinkronisasi penuh dari MySQL ke SQLite...');

        // Ensure nativephp connection is available
        if (!config()->has('database.connections.nativephp')) {
            config(['database.connections.nativephp' => [
                'driver' => 'sqlite',
                'url' => env('DATABASE_URL'),
                'database' => database_path('nativephp.sqlite'),
                'prefix' => '',
                'foreign_key_constraints' => env('DB_FOREIGN_KEYS', false), // Disable during import
            ]]);
        }

        // Get all tables from MySQL
        $tables = DB::connection('mysql')->select('SHOW TABLES');
        $dbName = env('DB_DATABASE', 'defaultdb');
        $key = 'Tables_in_' . $dbName;
        
        // If DB name is not defaultdb, we dynamically find the key
        if (!isset($tables[0]->$key)) {
             $key = array_keys((array)$tables[0])[0];
        }

        $tablesToIgnore = ['migrations', 'failed_jobs', 'cache', 'cache_locks', 'jobs', 'job_batches', 'sessions'];

        foreach ($tables as $tableObj) {
            $table = $tableObj->$key;

            if (in_array($table, $tablesToIgnore)) {
                continue;
            }

            $this->info("Memproses tabel: {$table}...");

            // 1. Dapatkan Schema MySQL
            $createStmt = DB::connection('mysql')->select("SHOW CREATE TABLE `{$table}`")[0]->{'Create Table'};

            // 2. Konversi Schema MySQL menjadi SQLite
            // Hapus parameter khusus MySQL (ENGINE, COLLATE, dll)
            $createStmt = preg_replace('/ENGINE=.*?$/i', '', $createStmt);
            $createStmt = preg_replace('/AUTO_INCREMENT=\d+/i', '', $createStmt);
            $createStmt = preg_replace('/DEFAULT CHARSET=[^\s]+/i', '', $createStmt);
            $createStmt = preg_replace('/COLLATE=[^\s]+/i', '', $createStmt);
            
            // Konversi tipe data AUTO_INCREMENT
            $createStmt = preg_replace('/int\(\d+\) NOT NULL AUTO_INCREMENT/i', 'INTEGER PRIMARY KEY AUTOINCREMENT', $createStmt);
            $createStmt = preg_replace('/bigint\(\d+\) NOT NULL AUTO_INCREMENT/i', 'INTEGER PRIMARY KEY AUTOINCREMENT', $createStmt);
            $createStmt = preg_replace('/int NOT NULL AUTO_INCREMENT/i', 'INTEGER PRIMARY KEY AUTOINCREMENT', $createStmt);
            $createStmt = preg_replace('/bigint unsigned NOT NULL AUTO_INCREMENT/i', 'INTEGER PRIMARY KEY AUTOINCREMENT', $createStmt);
            
            // SQLite tidak mendukung "ON UPDATE CURRENT_TIMESTAMP" secara langsung di deklarasi kolom biasa
            $createStmt = str_ireplace('ON UPDATE CURRENT_TIMESTAMP', '', $createStmt);
            
            // Hapus index/keys yang tidak disupport di baris Create Table SQLite (PRIMARY KEY sudah di atas)
            $createStmt = preg_replace('/,\s*(PRIMARY KEY|KEY|UNIQUE KEY|CONSTRAINT).*/s', "\n)", $createStmt);
            
            // Bersihkan sisa koma terakhir sebelum tutup kurung
            $createStmt = preg_replace('/,\s*\)/', "\n)", $createStmt);

            // 3. Drop dan Create di SQLite
            Schema::connection('nativephp')->dropIfExists($table);
            
            try {
                DB::connection('nativephp')->statement($createStmt);
            } catch (\Exception $e) {
                $this->warn("Gagal membuat tabel otomatis untuk {$table}. Mencoba metode fallback (hanya kolom)...");
                // Fallback jika konversi syntax gagal: buat tabel kosongan hanya dengan definisi kolom string/text (SANGAT BASIC)
                $columns = Schema::connection('mysql')->getColumnListing($table);
                Schema::connection('nativephp')->create($table, function($blueprint) use ($columns) {
                    foreach($columns as $col) {
                        if ($col === 'id') $blueprint->id();
                        else $blueprint->text($col)->nullable();
                    }
                });
            }

            // 4. Pindahkan Data
            $this->line("Menyalin data {$table}...");
            
            // Dapatkan nama kolom pertama untuk orderBy agar chunk tidak error jika tidak ada kolom 'id'
            $columnsList = Schema::connection('mysql')->getColumnListing($table);
            $orderByColumn = count($columnsList) > 0 ? $columnsList[0] : 'id';

            DB::connection('mysql')->table($table)->orderBy($orderByColumn)->chunk(500, function ($rows) use ($table) {
                $insertData = [];
                foreach ($rows as $row) {
                    $insertData[] = (array) $row;
                }
                
                if (count($insertData) > 0) {
                    DB::connection('nativephp')->table($table)->insert($insertData);
                }
            });

            $this->info("✓ Tabel {$table} berhasil disinkronisasi!");
        }

        $this->info('Sinkronisasi selesai! Semua tabel dan data telah dicopy ke SQLite secara ajaib.');
    }
}
