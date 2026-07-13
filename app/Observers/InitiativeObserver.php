<?php

namespace App\Observers;

use Modules\ITSP\Models\MstInitiative;
use App\Services\Shared\CacheManager;

class InitiativeObserver
{
    /**
     * Handle the MstInitiative "saved" event.
     * Triggered on create and update.
     */
    public function saved(MstInitiative $mstInitiative): void
    {
        $this->clearRelevantCaches();
    }

    /**
     * Handle the MstInitiative "deleted" event.
     */
    public function deleted(MstInitiative $mstInitiative): void
    {
        $this->clearRelevantCaches();
    }

    private function clearRelevantCaches(): void
    {
        CacheManager::clearInitiativeCaches();
        CacheManager::clearRoadmapCaches();
    }
}
