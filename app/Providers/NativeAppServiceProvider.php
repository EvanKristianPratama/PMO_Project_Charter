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
        Window::open()
            ->title('PMO Project Charter')
            ->width(1400)
            ->height(900)
            ->minWidth(1024)
            ->minHeight(680);
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

