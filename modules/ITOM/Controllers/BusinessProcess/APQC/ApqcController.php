<?php

namespace Modules\ITOM\Controllers\BusinessProcess\APQC;

use App\Http\Controllers\Controller;
use App\Models\MstApqc;
use App\Services\BusinessProcess\ApqcService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ApqcController extends Controller
{
    /**
     * Display a listing of APQC items.
     */
    public function index(ApqcService $apqcService): Response
    {
        return Inertia::render('modules/ITOM/BusinessProcess/APQC/Index', [
            'apqcList' => Inertia::defer(fn() => $apqcService->getApqcList()),
        ]);
    }

    /**
     * Store a newly created APQC item.
     */
    public function store(Request $request, ApqcService $apqcService): RedirectResponse
    {
        $validated = $request->validate([
            'parent_id' => 'nullable|exists:mst_apqc,id',
            'name' => 'required|string|max:255',
            'grup' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        $apqcService->createApqc($validated);

        return redirect()
            ->route('itom.business-process.apqc.index')
            ->with('success', 'APQC berhasil ditambahkan.');
    }

    /**
     * Update the specified APQC item.
     */
    public function update(Request $request, int $id, ApqcService $apqcService): RedirectResponse
    {
        $apqc = MstApqc::findOrFail($id);

        $validated = $request->validate([
            'parent_id' => 'nullable|exists:mst_apqc,id',
            'name' => 'required|string|max:255',
            'grup' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        $apqcService->updateApqc($apqc, $validated);

        return redirect()
            ->route('itom.business-process.apqc.index')
            ->with('success', 'APQC berhasil diperbarui.');
    }

    /**
     * Remove the specified APQC item.
     */
    public function destroy(int $id, ApqcService $apqcService): RedirectResponse
    {
        $apqc = MstApqc::findOrFail($id);
        
        $success = $apqcService->deleteApqc($apqc);

        if (!$success) {
            return redirect()
                ->route('itom.business-process.apqc.index')
                ->with('error', 'Tidak dapat menghapus APQC ini karena memiliki sub-proses.');
        }

        return redirect()
            ->route('itom.business-process.apqc.index')
            ->with('success', 'APQC berhasil dihapus.');
    }
}
