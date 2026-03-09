<?php

namespace App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Compendium;

use App\Http\Controllers\Controller;
use App\Models\MstInitiative;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CreateController extends Controller
{
    public function __invoke(): Response|RedirectResponse
    {
        if (request()->user()?->isAdminUser()) {
            return redirect()->route('admin.dashboard');
        }

        $initiativeOptions = MstInitiative::where('tipe_initiative', 1)
            ->with([
                'coe:id,name',
                'organization:id,name,groub_id',
                'organization.groub:id,name',
                'sourceData:id,name,month,year,created_at',
            ])
            ->orderBy('code')
            ->orderBy('name')
            ->get(['id', 'code', 'name', 'description', 'business_unit', 'coe_id', 'source'])
            ->map(function (MstInitiative $initiative): array {
                $source = $initiative->sourceData;

                $sourceCreated = '-';
                if ($source) {
                    if (!empty($source->month) && !empty($source->year)) {
                        $sourceCreated = $source->month . ' ' . $source->year;
                    } elseif (!empty($source->created_at)) {
                        $sourceCreated = $source->created_at->format('M Y');
                    }
                }

                return [
                    'id' => (int) $initiative->id,
                    'code' => $initiative->code,
                    'name' => $initiative->name,
                    'description' => $initiative->description,
                    'group' => $initiative->organization?->groub?->name ?? '-',
                    'project_owner' => $initiative->organization?->name ?? '-',
                    'coe' => $initiative->coe?->name ?? '-',
                    'data_source' => $source?->name ?? '-',
                    'data_source_created' => $sourceCreated,
                ];
            })
            ->values();

        return Inertia::render('ProgramPlanning/ProgramDefinition/DigitalInitiatives/Compendium/Show', [
            'initiativeOptions' => $initiativeOptions,
            'sourceOptions' => \App\Models\MstScSource::orderBy('name')->get(['id', 'name'])->values(),
        ]);
    }
}

