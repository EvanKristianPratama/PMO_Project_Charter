<?php

namespace App\Providers;

use Modules\ITSP\Models\Goal;
use Modules\ITSP\Models\InitiativeTagging;
use Modules\ITSP\Models\Milestone;
use App\Models\MstInitiative;
use App\Models\ProjectStatusHistory;
use Modules\ITSP\Models\Theme;
use Modules\ITSP\Models\TrsProject;
use Modules\ITSP\Models\TrsProjectCharter;
use App\Models\TrsStatusImplementation;
use App\Observers\InitiativeObserver;
use App\Observers\RoadmapObserver;
use App\Observers\StrategicPillarObserver;
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
        $this->registerObservers();
    }

    private function registerObservers(): void
    {
        // Initiative Observers
        MstInitiative::observe(InitiativeObserver::class);

        // Roadmap Observers
        TrsProject::observe(RoadmapObserver::class);
        TrsProjectCharter::observe(RoadmapObserver::class);
        Milestone::observe(RoadmapObserver::class);
        TrsStatusImplementation::observe(RoadmapObserver::class);
        ProjectStatusHistory::observe(RoadmapObserver::class);

        // Strategic Pillar Observers
        Goal::observe(StrategicPillarObserver::class);
        Theme::observe(StrategicPillarObserver::class);
        InitiativeTagging::observe(StrategicPillarObserver::class);
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
                                DB::reconnect($connection->getNameWithReadWriteType());
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
