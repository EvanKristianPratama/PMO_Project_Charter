<?php

namespace Modules\ITOM\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class ITOMServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Module name: lowercase 'itom'
        $moduleName = Str::lower(basename(dirname(__DIR__)));

        // Load Web Routes
        if (file_exists(__DIR__ . '/../Routes/web.php')) {
            Route::middleware(['web', 'auth'])
                ->prefix($moduleName)
                ->name($moduleName . '.')
                ->group(__DIR__ . '/../Routes/web.php');
        }
    }
}
