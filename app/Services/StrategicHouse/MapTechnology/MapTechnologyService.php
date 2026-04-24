<?php

namespace App\Services\StrategicHouse\MapTechnology;

use App\Models\MstInitiative;
use App\Models\MstCoe;
use App\Models\TrsMapTechnology;
use Illuminate\Support\Collection;

class MapTechnologyService
{
    public function getPageProps(): array
    {
        $mapTechnologies = TrsMapTechnology::with([
            'coe', 
            'initiative.organization', 
            'initiative.coe', 
            'initiative.latestStatusImplementation'
        ])
            ->orderBy('coe_id', 'asc')
            ->orderBy('initiative_id', 'asc')
            ->get()
            ->groupBy('coe_id');

        $coeOptions = MstCoe::all()->map(fn($coe) => [
            'id' => $coe->id,
            'name' => $coe->name,
        ]);

        $initiativeOptions = MstInitiative::query()
            ->select(['id', 'code', 'name'])
            ->orderBy('code')
            ->get();

        return [
            'mapTechnologies' => $mapTechnologies,
            'coeOptions' => $coeOptions,
            'initiativeOptions' => $initiativeOptions,
        ];
    }
}
