<?php

namespace Modules\ITOM\Controllers\BusinessProcess\BusinessProcess;

use App\Http\Controllers\Controller;
use App\Models\TrsProsesBisnis;
use App\Models\MstProsesBisnis;
use App\Services\BusinessProcess\BusinessProcessV2Service;
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
        $prosesBisnis = TrsProsesBisnis::with('organization')
            ->orderBy('organization_id')
            ->orderBy('no')
            ->get();

        $prosesBisnisV2 = $businessProcessV2Service->getProsesBisnisV2List();

        return Inertia::render('modules/ITOM/BusinessProcess/Index', [
            'prosesBisnis' => $prosesBisnis,
            'prosesBisnisV2' => $prosesBisnisV2,
        ]);
    }

    /**
     * Store a newly created Business Process v2 item.
     */
    public function storeProsesBisnisV2(Request $request, BusinessProcessV2Service $service): RedirectResponse
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
    public function updateProsesBisnisV2(Request $request, int $id, BusinessProcessV2Service $service): RedirectResponse
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
    public function destroyProsesBisnisV2(int $id, BusinessProcessV2Service $service): RedirectResponse
    {
        $item = MstProsesBisnis::findOrFail($id);
        
        $success = $service->delete($item);

        if (!$success) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus Proses Bisnis ini karena memiliki sub-proses.');
        }

        return redirect()->back()->with('success', 'Proses Bisnis v2 berhasil dihapus.');
    }
}
