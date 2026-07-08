<?php

namespace Modules\ITOM\Controllers\Regulation\CMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\MstDocument;
use App\Models\MstProsesBisnis;
use App\Services\BusinessProcess\BusinessProcess\BusinessProcessV2Service;

class CMSController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('modules/ITOM/Regulation/CMS/Index', [
            'documents' => Inertia::defer(fn() => MstDocument::all()->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'name' => $doc->name,
                    'url' => $doc->url,
                    'created_at' => $doc->created_at ? $doc->created_at->format('d M Y') : '-',
                    'updated_at' => $doc->updated_at ? $doc->updated_at->format('d M Y') : '-',
                ];
            })),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'nullable|string',
        ]);

        MstDocument::create($validated);

        return redirect()->back()->with('success', 'Dokumen berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $document = MstDocument::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'nullable|string',
        ]);

        $document->update($validated);

        return redirect()->back()->with('success', 'Dokumen berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $document = MstDocument::findOrFail($id);
        $document->delete();

        return redirect()->back()->with('success', 'Dokumen berhasil dihapus.');
    }

    public function regulationIndex(BusinessProcessV2Service $service): Response
    {
        return Inertia::render('modules/ITOM/Regulation/CMS/Regulation/Index', [
            'prosesBisnisV2' => Inertia::defer(fn() => $service->getProsesBisnisV2List()),
            'companyOptions' => Inertia::defer(fn() => \App\Models\MstCompany::orderBy('name')->get(['id', 'name'])),
            'regulations' => Inertia::defer(fn() => \App\Models\MstRegulation::orderBy('judul')->get(['id', 'judul', 'nomor', 'tipe', 'parent_id', 'status'])),
        ]);
    }

    public function storeRegulation(Request $request, BusinessProcessV2Service $service)
    {
        $validated = $request->validate([
            'company_id' => 'required|integer|exists:mst_company,id',
            'parent_id' => 'nullable|exists:mst_proses_bisnis,id',
            'name' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'order' => 'nullable|integer',
            'regulation_ids' => 'nullable|array',
            'regulation_ids.*' => 'integer|exists:mst_regulation,id',
        ]);

        $service->create($validated);

        return redirect()->back()->with('success', 'Proses Bisnis v2 berhasil ditambahkan.');
    }

    public function updateRegulation(Request $request, int $id, BusinessProcessV2Service $service)
    {
        $item = MstProsesBisnis::findOrFail($id);

        $validated = $request->validate([
            'company_id' => 'required|integer|exists:mst_company,id',
            'parent_id' => 'nullable|exists:mst_proses_bisnis,id',
            'name' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'order' => 'nullable|integer',
            'regulation_ids' => 'nullable|array',
            'regulation_ids.*' => 'integer|exists:mst_regulation,id',
        ]);

        $service->update($item, $validated);

        return redirect()->back()->with('success', 'Proses Bisnis v2 berhasil diperbarui.');
    }

    public function destroyRegulation(int $id, BusinessProcessV2Service $service)
    {
        $item = MstProsesBisnis::findOrFail($id);
        
        $success = $service->delete($item);

        if (!$success) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus Proses Bisnis ini karena memiliki sub-proses.');
        }

        return redirect()->back()->with('success', 'Proses Bisnis v2 berhasil dihapus.');
    }
}