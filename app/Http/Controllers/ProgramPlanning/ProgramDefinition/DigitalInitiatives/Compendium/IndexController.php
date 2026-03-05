<?php

namespace App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Compendium;

use App\Http\Controllers\Controller;
use App\Models\ScDetail;
use App\Models\ScInitiative;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
    public function __invoke(): Response|RedirectResponse
    {
        if (request()->user()?->isAdminUser()) {
            return redirect()->route('admin.dashboard');
        }

        $compendiumItems = ScInitiative::query()
            ->with([
                'masterInitiative:id,code,name,description,coe_id,business_unit,source',
                'masterInitiative.coe:id,name',
                'masterInitiative.organization:id,name,groub_id',
                'masterInitiative.organization.groub:id,name',
                'masterInitiative.sourceData:id,name,month,year,created_at',
                'scDetails' => fn ($query) => $query->latest('id'),
            ])
            ->whereNotNull('initiative_id')
            ->orderBy('id')
            ->get([
                'id',
                'initiative_id',
                'alias',
                'useCase_description',
                'value',
                'urgency',
            ])
            ->map(function (ScInitiative $item): array {
                $detail = $item->scDetails->first();
                $source = $item->masterInitiative?->sourceData;
                $sourceCreatedAt = '-';

                if ($source) {
                    if (!empty($source->month) && !empty($source->year)) {
                        $sourceCreatedAt = $source->month . ' ' . $source->year;
                    } elseif (!empty($source->created_at)) {
                        $sourceCreatedAt = $source->created_at->format('M Y');
                    }
                }

                return [
                    'id' => (int) $item->id,
                    'initiative_id' => $item->initiative_id ? (int) $item->initiative_id : null,
                    'group' => $item->masterInitiative?->organization?->groub?->name ?? '-',
                    'no' => $item->masterInitiative?->code ?? '-',
                    'project_owner' => $item->masterInitiative?->organization?->name ?? '-',
                    'use_case' => $item->masterInitiative?->name ?? '-',
                    'desc' => $item->useCase_description ?: ($item->masterInitiative?->description ?? '-'),
                    'value' => $this->scoreLabel($item->value),
                    'urgency' => $this->scoreLabel($item->urgency),
                    'rjpp' => $this->resolveRjpp($detail),
                    'coe' => $item->masterInitiative?->coe?->name ?? '-',
                    'data_source' => $source?->name ?? '-',
                    'data_source_created' => $sourceCreatedAt,
                    'detail' => $detail ? [
                        'use_case_description' => $detail->useCase_description,
                        'current_situation' => $detail->current_situation,
                        'key_functionalities' => $detail->key_functionalities,
                        'value_detail' => $detail->value_detail,
                        'urgency_detail' => $detail->urgency_detail,
                        'ease_implementation' => (int) $detail->ease_implementation,
                        'ease_detail' => $detail->ease_detail,
                        'resource_requirement' => (int) $detail->resource_requirement,
                        'resource_detail' => $detail->resource_detail,
                        'interpendencies' => $detail->interpendencies,
                        'sign_by' => $detail->sign_by,
                    ] : null,
                ];
            })
            ->values();

        return Inertia::render('ProgramPlanning/ProgramDefinition/DigitalInitiatives/Compendium/Index', [
            'compendiumItems' => $compendiumItems,
            'totalCompendiumItems' => $compendiumItems->count(),
        ]);
    }

    private function scoreLabel(?int $score): string
    {
        return match ((int) $score) {
            1 => 'Low',
            2 => 'Medium',
            3 => 'High',
            4 => 'TBC',
            default => '-',
        };
    }

    private function resolveRjpp(?ScDetail $detail): string
    {
        if (!$detail) {
            return '-';
        }

        $raw = trim((string) ($detail->value_detail ?? ''));
        if ($raw === '' || $raw === '-') {
            return '-';
        }

        return $raw;
    }
}
