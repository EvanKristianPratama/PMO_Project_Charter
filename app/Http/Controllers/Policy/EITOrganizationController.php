<?php

namespace App\Http\Controllers\Policy;

use App\Http\Controllers\Controller;
use App\Models\MstItSteeringComittee;
use App\Models\TrsOrganization;
use Inertia\Inertia;
use Inertia\Response;

class EITOrganizationController extends Controller
{
    public function index(): Response
    {
        $organizations = TrsOrganization::query()
            ->where('code', '1000000')
            ->orWhere('code', '1700000')
            ->orWhere('code', 'like', '171%')
            ->with([
                'groub.company',
                'picOrganization' => fn ($query) => $query->select(['id', 'organization_id', 'name'])
            ])
            ->get();

        $rows = $organizations->map(function ($org) {
            return [
                'organization_id' => (int) $org->id,
                'code' => trim((string) ($org->code ?? '')),
                'organization_code' => trim((string) ($org->code ?? '')),
                'organization_name' => $org->name,
                'alias' => $org->alias,
                'jabatan' => $org->jabatan,
                'pejabat' => $org->pejabat,
                'groub_id' => (int) ($org->groub_id ?? 0),
                'groub_name' => $org->groub?->name ?? 'Tanpa Sub Holding',
                'company_id' => $org->groub?->company?->id ? (int) $org->groub->company->id : null,
                'company_name' => $org->groub?->company?->name ?? 'Tanpa Holding',
                'pic_projects' => $org->picOrganization->map(fn ($pic) => [
                    'id' => $pic->id,
                    'name' => $pic->name,
                ])->values()->all(),
            ];
        })
        ->sortBy([
            ['company_name', 'asc'],
            ['groub_name', 'asc'],
            ['code', 'asc'],
        ])
        ->values()
        ->all();

        $steeringRows = MstItSteeringComittee::with('organization')
            ->orderBy('code')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'code' => trim((string) ($item->code ?? '')),
                    'organization_id' => $item->organization_id,
                    'organization_name' => $item->organization?->jabatan ?? '-',
                ];
            })
            ->values()
            ->all();

        return Inertia::render('Policy/Organization/Index', [
            'organizationStructureRows' => $rows,
            'steeringRows' => $steeringRows,
        ]);
    }
}
