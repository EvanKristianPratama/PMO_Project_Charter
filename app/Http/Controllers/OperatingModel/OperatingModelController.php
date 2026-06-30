<?php

namespace App\Http\Controllers\OperatingModel;

use App\Http\Controllers\Controller;
use App\Models\MstBod;
use App\Models\MstItSteeringComittee;
use App\Models\TrsOrganization;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OperatingModelController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('OperatingModel/Index');
    }

    public function itGovernance(): Response
    {
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

        return Inertia::render('OperatingModel/ItGovernance/Index', [
            'steeringRows' => $steeringRows,
            'organizationOptions' => TrsOrganization::orderBy('name')->get(['id', 'name', 'jabatan']),
        ]);
    }

    public function itManagement(): Response
    {
        // Target IDs to always include:
        // 1: Direktur Utama
        // 5: Direktur Penunjang Bisnis
        // 67: SVP Enterprise IT
        $ids = [1, 5, 67];

        // Recursive query to retrieve all descendant IDs of parent 67
        $getDescendants = function ($parentIds) use (&$getDescendants) {
            if (empty($parentIds)) {
                return [];
            }
            $children = MstBod::whereIn('parent_id', $parentIds)->pluck('id')->toArray();
            if (empty($children)) {
                return [];
            }
            return array_merge($children, $getDescendants($children));
        };

        $descendantIds = $getDescendants([67]);
        $allIds = array_merge($ids, $descendantIds);

        $bods = MstBod::with('company')
            ->whereIn('id', $allIds)
            ->get();

        $rows = $bods->map(function ($bod) {
            return [
                'organization_id' => (int) $bod->id,
                'parent_id' => $bod->parent_id ? (int) $bod->parent_id : null,
                'organization_name' => $bod->name,
                'alias' => $bod->alias,
                'pejabat' => $bod->pejabat,
                'groub_id' => 0,
                'groub_name' => 'Holding',
                'company_id' => $bod->company_id ? (int) $bod->company_id : null,
                'company_name' => $bod->company?->name ?? 'Tanpa Holding',
                'order' => $bod->order,
            ];
        })
        ->sortBy([
            ['company_name', 'asc'],
            ['groub_name', 'asc'],
            ['order', 'asc'],
            ['organization_name', 'asc'],
        ])
        ->values()
        ->all();

        return Inertia::render('OperatingModel/ItManagement/Index', [
            'organizationStructureRows' => $rows,
        ]);
    }

    public function storeSteering(Request $request)
    {
        $validated = $request->validate([
            'organization_id' => 'required|exists:trs_organization,id',
            'code' => 'required|string|size:8',
        ]);

        MstItSteeringComittee::create($validated);

        return redirect()->back()->with('success', 'Data Steering Committee berhasil ditambahkan.');
    }

    public function updateSteering(Request $request, $id)
    {
        $validated = $request->validate([
            'organization_id' => 'required|exists:trs_organization,id',
            'code' => 'required|string|size:8',
        ]);

        $item = MstItSteeringComittee::findOrFail($id);
        $item->update($validated);

        return redirect()->back()->with('success', 'Data Steering Committee berhasil diperbarui.');
    }

    public function destroySteering($id)
    {
        $item = MstItSteeringComittee::findOrFail($id);
        $item->delete();

        return redirect()->back()->with('success', 'Data Steering Committee berhasil dihapus.');
    }
}
