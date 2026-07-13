<?php

namespace Modules\ITOM\Controllers\Regulation\CMS;

use App\Http\Controllers\Controller;
use Modules\ITOM\Models\MstCompany;
use Modules\ITOM\Models\MstDocument;
use Modules\ITOM\Models\MstProsesBisnis;
use Modules\ITOM\Models\MstRegulation;
use Modules\ITOM\Services\BusinessProcess\BusinessProcess\BusinessProcessV2Service;
use Modules\ITOM\Services\Regulation\CMS\CmsService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CMSController extends Controller
{
    protected CmsService $cmsService;

    public function __construct(CmsService $cmsService)
    {
        $this->cmsService = $cmsService;
    }

    public function index(Request $request): Response
    {
        return Inertia::render('modules/ITOM/Regulation/CMS/Index', [
            'documents' => Inertia::defer(fn() => $this->cmsService->getDocumentsWithRegulations()),
            'regulationOptions' => Inertia::defer(fn() => $this->cmsService->getRegulationOptions()),
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
            'prosesBisnisV2'      => Inertia::defer(fn() => $service->getProsesBisnisV2List()),
            'companyOptions'      => Inertia::defer(fn() => MstCompany::orderBy('name')->get(['id', 'name'])),
            'regulations'         => Inertia::defer(fn() => MstRegulation::orderBy('judul')->get(['id', 'judul', 'nomor', 'tipe', 'parent_id', 'status'])),
            'regulationDocuments' => Inertia::defer(fn() => $this->cmsService->getRegulationsWithDocuments()),
        ]);
    }

    public function storeRegulation(Request $request)
    {
        $validated = $request->validate([
            'document_id' => 'required|integer|exists:mst_document,id',
            'regulation_id' => 'required|integer|exists:mst_regulation,id',
        ]);

        $mapped = $this->cmsService->mapRegulation(
            $validated['document_id'],
            $validated['regulation_id']
        );

        if (!$mapped) {
            return redirect()->back()->withErrors([
                'regulation_id' => 'Dokumen ini sudah dimapping ke regulasi yang dipilih.'
            ]);
        }

        return redirect()->back()->with('success', 'Mapping dokumen berhasil disimpan.');
    }

    public function updateRegulation(Request $request, $id)
    {
        // Route exists in web.php but update mapping isn't logically needed.
        // Left as stub for backward compatibility.
        return redirect()->back();
    }

    public function destroyRegulation($document_id, $regulation_id)
    {
        $this->cmsService->unmapRegulation((int)$document_id, (int)$regulation_id);

        return redirect()->back()->with('success', 'Mapping dokumen berhasil dihapus.');
    }
}