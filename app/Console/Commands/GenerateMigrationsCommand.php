<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class GenerateMigrationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-migrations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Laravel migration files from the remote MySQL Aiven database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Mempersiapkan pembuatan file migrasi dari Aiven MySQL...');

        // Override koneksi mysql agar mengarah ke database cloud Aiven
        config(['database.connections.mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_CLOUD_HOST', 'mysql-3fb43829-pmopc01.h.aivencloud.com'),
            'port' => env('DB_CLOUD_PORT', '14759'),
            'database' => env('DB_CLOUD_DATABASE', 'defaultdb'),
            'username' => env('DB_CLOUD_USERNAME', 'avnadmin'),
            'password' => env('DB_CLOUD_PASSWORD'),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                \PDO::MYSQL_ATTR_SSL_CA => base_path('ca.pem'),
            ]) : [],
        ]]);

        // Tentukan folder tujuan migrasi
        $migrationDir = database_path('migrations');
        if (!File::exists($migrationDir)) {
            File::makeDirectory($migrationDir, 0755, true);
        } else {
            // Bersihkan isi folder lama untuk menghindari duplikasi file lama
            File::cleanDirectory($migrationDir);
            $this->warn('Folder database/migrations dibersihkan terlebih dahulu.');
        }

        // Ambil daftar tabel dari MySQL
        $tables = DB::connection('mysql')->select('SHOW TABLES');
        $dbName = 'defaultdb';
        $key = 'Tables_in_' . $dbName;
        
        if (empty($tables)) {
            $this->error('Tidak ada tabel yang ditemukan di MySQL.');
            return 1;
        }

        if (!isset($tables[0]->$key)) {
            $key = array_keys((array)$tables[0])[0];
        }

        $tablesToIgnore = ['migrations', 'failed_jobs', 'cache', 'cache_locks', 'jobs', 'job_batches', 'sessions'];
        $index = 0;

        foreach ($tables as $tableObj) {
            $table = $tableObj->$key;

            if (in_array($table, $tablesToIgnore)) {
                continue;
            }

            $this->info("Membuat migrasi untuk tabel: {$table}...");

            // Mengambil detail kolom dari MySQL
            $columns = DB::connection('mysql')->select("SHOW COLUMNS FROM `{$table}`");
            
            $columnLines = [];
            $hasCreatedAt = false;
            $hasUpdatedAt = false;

            // Periksa timestamps
            foreach ($columns as $column) {
                if ($column->Field === 'created_at') $hasCreatedAt = true;
                if ($column->Field === 'updated_at') $hasUpdatedAt = true;
            }

            foreach ($columns as $column) {
                $name = $column->Field;
                $type = $column->Type;
                $nullable = $column->Null === 'YES';
                $default = $column->Default;
                $keyType = $column->Key;
                $extra = $column->Extra;

                // Jika kolom merupakan bagian dari timestamps, lewati dulu
                if (($name === 'created_at' && $hasCreatedAt && $hasUpdatedAt) || 
                    ($name === 'updated_at' && $hasCreatedAt && $hasUpdatedAt)) {
                    continue;
                }

                $definition = "\$table->";

                // Deteksi tipe data
                if ($name === 'id' && str_contains($extra, 'auto_increment')) {
                    $definition .= "id()";
                } else {
                    if (str_contains($type, 'int') && str_contains($extra, 'auto_increment')) {
                        $definition .= "id('{$name}')";
                    } elseif (str_contains($type, 'bigint')) {
                        $definition .= "bigInteger('{$name}')";
                    } elseif (str_contains($type, 'tinyint(1)') || $type === 'tinyint(1)' || $type === 'boolean') {
                        $definition .= "boolean('{$name}')";
                    } elseif (str_contains($type, 'tinyint')) {
                        $definition .= "tinyInteger('{$name}')";
                    } elseif (str_contains($type, 'smallint')) {
                        $definition .= "smallInteger('{$name}')";
                    } elseif (str_contains($type, 'mediumint')) {
                        $definition .= "mediumInteger('{$name}')";
                    } elseif (str_contains($type, 'int')) {
                        $definition .= "integer('{$name}')";
                    } elseif (str_contains($type, 'varchar')) {
                        preg_match('/\d+/', $type, $matches);
                        $length = $matches[0] ?? 255;
                        $definition .= "string('{$name}', {$length})";
                    } elseif (str_contains($type, 'char')) {
                        preg_match('/\d+/', $type, $matches);
                        $length = $matches[0] ?? 255;
                        $definition .= "char('{$name}', {$length})";
                    } elseif ($type === 'text') {
                        $definition .= "text('{$name}')";
                    } elseif ($type === 'mediumtext') {
                        $definition .= "mediumText('{$name}')";
                    } elseif ($type === 'longtext') {
                        $definition .= "longText('{$name}')";
                    } elseif (str_contains($type, 'decimal')) {
                        preg_match_all('/\d+/', $type, $matches);
                        $precision = $matches[0][0] ?? 8;
                        $scale = $matches[0][1] ?? 2;
                        $definition .= "decimal('{$name}', {$precision}, {$scale})";
                    } elseif ($type === 'double') {
                        $definition .= "double('{$name}')";
                    } elseif ($type === 'float') {
                        $definition .= "float('{$name}')";
                    } elseif ($type === 'date') {
                        $definition .= "date('{$name}')";
                    } elseif ($type === 'datetime') {
                        $definition .= "dateTime('{$name}')";
                    } elseif ($type === 'timestamp') {
                        $definition .= "timestamp('{$name}')";
                    } elseif ($type === 'time') {
                        $definition .= "time('{$name}')";
                    } elseif ($type === 'json') {
                        $definition .= "json('{$name}')";
                    } else {
                        $definition .= "string('{$name}')";
                    }

                    // Tambahan nullable
                    if ($nullable) {
                        $definition .= "->nullable()";
                    }

                    // Tambahan default value
                    if ($default !== null) {
                        if ($default === 'CURRENT_TIMESTAMP') {
                            $definition .= "->useCurrent()";
                        } elseif (is_numeric($default)) {
                            $definition .= "->default({$default})";
                        } else {
                            $definition .= "->default('{$default}')";
                        }
                    }

                    // Tambahan unique
                    if ($keyType === 'UNI') {
                        $definition .= "->unique()";
                    }
                }

                $columnLines[] = "            " . $definition . ";";
            }

            if ($hasCreatedAt && $hasUpdatedAt) {
                $columnLines[] = "            \$table->timestamps();";
            }

            $columnsStr = implode("\n", $columnLines);

            // Buat nama file migrasi berurutan
            $datePrefix = date('Y_m_d_') . str_pad((string) $index, 6, '0', STR_PAD_LEFT);
            $fileName = "{$datePrefix}_create_{$table}_table.php";
            $filePath = $migrationDir . '/' . $fileName;

            $template = <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('{$table}', function (Blueprint \$table) {
{$columnsStr}
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('{$table}');
    }
};
PHP;

            File::put($filePath, $template);
            $this->info("✓ Berhasil membuat file migrasi: {$fileName}");
            $index++;
        }

        $this->info('Semua file migrasi berhasil dibuat secara dinamis di database/migrations!');
        return 0;
    }
}
