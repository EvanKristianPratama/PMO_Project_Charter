<?php

namespace Modules\ITOM\Controllers\BusinessProcess\Kpi;

use App\Http\Controllers\Controller;
use Modules\ITOM\Models\MstKpi;
use Modules\ITOM\Services\BusinessProcess\KPI\KpiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class KpiController extends Controller
{
    /**
     * Display a listing of KPI items.
     */
    public function index(KpiService $kpiService): Response
    {
        return Inertia::render('modules/ITOM/BusinessProcess/KPI/Index', [
            'kpiList' => Inertia::defer(fn() => $kpiService->getKpis()),
            'companyOptions' => Inertia::defer(fn() => \Modules\ITOM\Models\MstCompany::orderBy('name')->get(['id', 'name'])),
        ]);
    }

    /**
     * Store a newly created KPI item.
     */
    public function storeKpi(Request $request, KpiService $kpiService): RedirectResponse
    {
        $validated = $request->validate([
            'deskripsi' => ['required', 'string'],
            'company_id' => ['nullable', 'integer', 'exists:mst_company,id'],
        ]);

        $kpiService->createKpi($validated);

        return redirect()->back()->with('success', 'KPI berhasil ditambahkan.');
    }

    /**
     * Update the specified KPI item.
     */
    public function updateKpi(Request $request, int $id, KpiService $kpiService): RedirectResponse
    {
        $kpi = MstKpi::findOrFail($id);

        $validated = $request->validate([
            'deskripsi' => ['required', 'string'],
            'company_id' => ['nullable', 'integer', 'exists:mst_company,id'],
        ]);

        $kpiService->updateKpi($kpi, $validated);

        return redirect()->back()->with('success', 'KPI berhasil diperbarui.');
    }

    /**
     * Remove the specified KPI item.
     */
    public function destroyKpi(int $id, KpiService $kpiService): RedirectResponse
    {
        $kpi = MstKpi::findOrFail($id);
        $kpiService->deleteKpi($kpi);

        return redirect()->back()->with('success', 'KPI berhasil dihapus.');
    }
}
