<?php

namespace App\Services\StrategicHouse\MapTechnology;

use App\Models\MstInitiative;
use App\Models\MstCoe;
use Modules\ITSP\Models\TrsMapTechnology;
use Illuminate\Support\Facades\Cache;

class MapTechnologyService
{
    public function getPageProps(): array
    {
        return Cache::remember('sh_map_technology_props_v1', 3600, function () {
            $mapTechnologies = TrsMapTechnology::with([
                'coe:id,name',
                'initiative:id,code,name,coe_id,business_unit',
                'initiative.organization:id,name',
                'initiative.coe:id,name',
                'initiative.latestStatusImplementation:id,initiative_id,review_status',
            ])
                ->orderBy('coe_id', 'asc')
                ->orderBy('initiative_id', 'asc')
                ->get()
                ->groupBy('coe_id');

            $mappedCoeIds = MstInitiative::query()
                ->whereIn('id', TrsMapTechnology::distinct()->pluck('initiative_id'))
                ->whereNotNull('coe_id')
                ->distinct()
                ->pluck('coe_id');

            $coeOptions = MstCoe::query()
                ->whereIn('id', $mappedCoeIds)
                ->orderBy('id')
                ->get(['id', 'name'])
                ->map(fn($coe) => [
                    'id' => $coe->id,
                    'name' => $coe->name,
                ]);

            $initiativeOptions = MstInitiative::query()
                ->select(['id', 'code', 'name', 'tipe_initiative'])
                ->where('tipe_initiative', 2)
                ->orderBy('code')
                ->get();

            return [
                'mapTechnologies' => $mapTechnologies,
                'coeOptions' => $coeOptions,
                'initiativeOptions' => $initiativeOptions,
            ];
        });
    }
}
