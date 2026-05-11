<?php

namespace App\Providers;

use Native\Laravel\Facades\Window;
use Native\Laravel\Contracts\ProvidesPhpIni;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     *
     * Note: The app connects to the remote MySQL (Aiven) database
     * as configured in .env (DB_CONNECTION=mysql).
     * NativePHP's internal SQLite is only used for framework internals.
     */
    public function boot(): void
    {
        // Menyalin database SQLite bawaan (pre-populated) ke jalur aktif agar data langsung siap pakai
        try {
            if (!config('app.debug')) {
                // Di mode production, salin ke jalur database internal NativePHP (Application Support)
                $prodDatabasePath = config('nativephp-internal.database_path');
                $bundledDatabasePath = database_path('nativephp.sqlite');

                if ($prodDatabasePath) {
                    $dir = dirname($prodDatabasePath);
                    if (!file_exists($dir)) {
                        mkdir($dir, 0755, true);
                    }

                    if (!file_exists($prodDatabasePath) || filesize($prodDatabasePath) === 0) {
                        if (file_exists($bundledDatabasePath) && filesize($bundledDatabasePath) > 0) {
                            copy($bundledDatabasePath, $prodDatabasePath);
                        } else {
                            // Fallback: create empty database and run migrations locally
                            touch($prodDatabasePath);
                            try {
                                \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
                            } catch (\Exception $migEx) {
                                \Log::error('Gagal menjalankan migrasi otomatis: ' . $migEx->getMessage());
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error('Gagal menyalin database SQLite bawaan: ' . $e->getMessage());
        }

        $window = Window::open()
            ->title('PMO Project Charter')
            ->width(1400)
            ->height(900)
            ->minWidth(1024)
            ->minHeight(680);

        if (config('app.debug')) {
            $window->showDevTools();
        }
    }

    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {
        return [
            'memory_limit' => '512M',
            'max_execution_time' => '300',
        ];
    }
}

