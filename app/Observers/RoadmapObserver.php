<?php

namespace App\Observers;

use App\Services\Shared\CacheManager;

class RoadmapObserver
{
    /**
     * Handle the model "saved" event.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     */
    public function saved($model): void
    {
        CacheManager::clearRoadmapCaches();
    }

    /**
     * Handle the model "deleted" event.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     */
    public function deleted($model): void
    {
        CacheManager::clearRoadmapCaches();
    }
}
