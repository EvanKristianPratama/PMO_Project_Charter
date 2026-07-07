<?php

namespace Modules\ITOM\Controllers\BusinessProcess\BusinessProcess;

use App\Http\Controllers\Controller;
use App\Models\TrsProsesBisnis;
use App\Models\MstProsesBisnis;
use App\Services\BusinessProcess\BusinessProcess\BusinessProcessV2Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BusinessProcessController extends Controller
{
    /**
     * Display a listing of business processes.
     */
    public function index(BusinessProcessV2Service $businessProcessV2Service): Response
    {
        return Inertia::render('modules/ITOM/BusinessProcess/BusinessProcess/Index', [
            'prosesBisnisV2' => Inertia::defer(fn() => $businessProcessV2Service->getProsesBisnisV2List()),
            'companyOptions' => Inertia::defer(fn() => \App\Models\MstCompany::orderBy('name')->get(['id', 'name'])),
            'kpiList' => Inertia::defer(fn() => \App\Models\MstKpi::orderBy('deskripsi')->get(['id', 'deskripsi'])),
            'regulations' => Inertia::defer(fn() => \App\Models\MstRegulation::orderBy('judul')->get(['id', 'judul', 'nomor', 'tipe', 'parent_id', 'status'])),
        ]);
    }

    /**
     * Store a newly created Business Process v2 item.
     */
    public function storeBusinessProcessV2(Request $request, BusinessProcessV2Service $service): RedirectResponse
    {
        $validated = $request->validate([
            'company_id' => 'required|integer|exists:mst_company,id',
            'parent_id' => 'nullable|exists:mst_proses_bisnis,id',
            'name' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'order' => 'nullable|integer',
            'kpi_ids' => 'nullable|array',
            'kpi_ids.*' => 'integer|exists:mst_kpi,id',
            'regulation_ids' => 'nullable|array',
            'regulation_ids.*' => 'integer|exists:mst_regulation,id',
        ]);

        $service->create($validated);

        return redirect()->back()->with('success', 'Proses Bisnis v2 berhasil ditambahkan.');
    }

    /**
     * Update the specified Business Process v2 item.
     */
    public function updateBusinessProcessV2(Request $request, int $id, BusinessProcessV2Service $service): RedirectResponse
    {
        $item = MstProsesBisnis::findOrFail($id);

        $validated = $request->validate([
            'company_id' => 'required|integer|exists:mst_company,id',
            'parent_id' => 'nullable|exists:mst_proses_bisnis,id',
            'name' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'order' => 'nullable|integer',
            'kpi_ids' => 'nullable|array',
            'kpi_ids.*' => 'integer|exists:mst_kpi,id',
            'regulation_ids' => 'nullable|array',
            'regulation_ids.*' => 'integer|exists:mst_regulation,id',
        ]);

        $service->update($item, $validated);

        return redirect()->back()->with('success', 'Proses Bisnis v2 berhasil diperbarui.');
    }

    /**
     * Remove the specified Business Process v2 item.
     */
    public function destroyBusinessProcessV2(int $id, BusinessProcessV2Service $service): RedirectResponse
    {
        $item = MstProsesBisnis::findOrFail($id);
        
        $success = $service->delete($item);

        if (!$success) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus Proses Bisnis ini karena memiliki sub-proses.');
        }

        return redirect()->back()->with('success', 'Proses Bisnis v2 berhasil dihapus.');
    }
}
