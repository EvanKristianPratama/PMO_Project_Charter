<?php

namespace App\Providers;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureUrl();
        $this->configureDbReconnect();
    }

    private function configureUrl(): void
    {
        $appUrl = rtrim((string) config('app.url'), '/');

        if ($appUrl === '' || $this->app->environment(['local', 'testing'])) {
            return;
        }

        URL::forceRootUrl($appUrl);

        if (str_starts_with($appUrl, 'https://')) {
            URL::forceScheme('https');
        }
    }

    /**
     * Automatically retry database queries when the SSL connection drops.
     * This handles the intermittent "Cannot connect to MySQL using SSL" errors
     * from the remote Aiven cloud database.
     */
    private function configureDbReconnect(): void
    {
        $this->app->booted(function () {
            foreach (['mysql', 'cloud'] as $connectionName) {
                try {
                    $connection = DB::connection($connectionName);
                    $connection->setReconnector(function ($connection) {
                        $maxRetries = 3;
                        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
                            try {
                                $connection->disconnect();
                                $connection->reconnect();
                                return;
                            } catch (\Throwable $e) {
                                if ($attempt >= $maxRetries) {
                                    throw $e;
                                }
                                usleep(500_000 * $attempt); // 0.5s, 1s, 1.5s
                            }
                        }
                    });
                } catch (\Throwable) {
                    // Connection not configured — skip.
                }
            }
        });
    }
}
