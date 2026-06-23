<?php

namespace App\Http\Controllers\Architecture\ProsesBisnis;

use App\Http\Controllers\Controller;
use App\Models\TrsOrganization;
use App\Models\TrsProsesBisnis;
use App\Models\MstFunction;
use App\Models\MstApqc;
use App\Models\MstCompany;
use App\Services\Architecture\ApqcService;
use App\Services\Architecture\FunctionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProsesBisnisController extends Controller
{
    /**
     * Display a listing of business processes.
     */
    public function index(ApqcService $apqcService, FunctionService $functionService): Response
    {
        $prosesBisnis = TrsProsesBisnis::with('organization')
            ->orderBy('organization_id')
            ->orderBy('no')
            ->get();

        $organizations = TrsOrganization::orderBy('name')->get();

        $functions = $functionService->getFunctions();


        $companyOptions = MstCompany::orderBy('name')->get()->map(fn ($c) => [
            'id' => $c->id,
            'name' => $c->name,
        ])->values()->all();

        $regulations = \App\Models\MstRegulation::orderBy('judul')->get()->map(fn ($r) => [
            'id' => $r->id,
            'judul' => $r->judul,
            'nomor' => $r->nomor,
            'tipe' => $r->tipe,
        ])->values()->all();

        $apqcList = $apqcService->getApqcList();

        return Inertia::render('Architecture/ProsesBisnis/Index', [
            'prosesBisnis' => $prosesBisnis,
            'organizations' => $organizations,
            'functions' => $functions,
            'companyOptions' => $companyOptions,
            'regulations' => $regulations,
            'apqcList' => $apqcList,
        ]);
    }

    /**
     * Store a newly created business process.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'organization_id' => 'required|exists:trs_organization,id',
            'no' => 'required|string',
            'proses_bisnis' => 'required|string',
            'tugas' => 'required|string',
            'hasil' => 'required|string',
            'status' => 'nullable|string',
        ]);

        TrsProsesBisnis::create($validated);

        return redirect()
            ->route('architecture.proses-bisnis.index')
            ->with('success', 'Proses Bisnis berhasil ditambahkan.');
    }

    /**
     * Update the specified business process.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $prosesBisnis = TrsProsesBisnis::findOrFail($id);

        $validated = $request->validate([
            'organization_id' => 'required|exists:trs_organization,id',
            'no' => 'required|string',
            'proses_bisnis' => 'required|string',
            'tugas' => 'required|string',
            'hasil' => 'required|string',
            'status' => 'nullable|string',
        ]);

        $prosesBisnis->update($validated);

        return redirect()
            ->route('architecture.proses-bisnis.index')
            ->with('success', 'Proses Bisnis berhasil diperbarui.');
    }

    /**
     * Remove the specified business process.
     */
    public function destroy(int $id): RedirectResponse
    {
        $prosesBisnis = TrsProsesBisnis::findOrFail($id);
        $prosesBisnis->delete();

        return redirect()
            ->route('architecture.proses-bisnis.index')
            ->with('success', 'Proses Bisnis berhasil dihapus.');
    }

    /**
     * Store a newly created APQC item.
     */
    public function storeApqc(Request $request, ApqcService $apqcService): RedirectResponse
    {
        $validated = $request->validate([
            'parent_id' => 'nullable|exists:mst_apqc,id',
            'name' => 'required|string|max:255',
        ]);

        $apqcService->createApqc($validated);

        return redirect()
            ->route('architecture.proses-bisnis.index')
            ->with('success', 'APQC berhasil ditambahkan.');
    }

    /**
     * Update the specified APQC item.
     */
    public function updateApqc(Request $request, int $id, ApqcService $apqcService): RedirectResponse
    {
        $apqc = MstApqc::findOrFail($id);

        $validated = $request->validate([
            'parent_id' => 'nullable|exists:mst_apqc,id',
            'name' => 'required|string|max:255',
        ]);

        $apqcService->updateApqc($apqc, $validated);

        return redirect()
            ->route('architecture.proses-bisnis.index')
            ->with('success', 'APQC berhasil diperbarui.');
    }

    /**
     * Remove the specified APQC item.
     */
    public function destroyApqc(int $id, ApqcService $apqcService): RedirectResponse
    {
        $apqc = MstApqc::findOrFail($id);
        
        $success = $apqcService->deleteApqc($apqc);

        if (!$success) {
            return redirect()
                ->route('architecture.proses-bisnis.index')
                ->with('error', 'Tidak dapat menghapus APQC ini karena memiliki sub-proses.');
        }

        return redirect()
            ->route('architecture.proses-bisnis.index')
            ->with('success', 'APQC berhasil dihapus.');
    }

    /**
     * Store a newly created Function item.
     */
    public function storeFunction(Request $request, FunctionService $functionService): RedirectResponse
    {
        $validated = $request->validate([
            'company_id'     => ['required', 'integer', 'exists:mst_company,id'],
            'parent_id'      => ['nullable', 'integer', 'exists:mst_function,id'],
            'code'           => ['required', 'string', 'max:255'],
            'name'           => ['required', 'string', 'max:255'],
            'alias'          => ['nullable', 'string', 'max:255'],
            'regulation_ids' => ['nullable', 'array'],
            'regulation_ids.*' => ['integer', 'exists:mst_regulation,id'],
        ]);

        $functionService->createFunction($validated);

        return redirect()->back()->with('success', 'Function berhasil ditambahkan.');
    }

    /**
     * Update the specified Function item.
     */
    public function updateFunction(Request $request, int $id, FunctionService $functionService): RedirectResponse
    {
        $function = MstFunction::findOrFail($id);

        $validated = $request->validate([
            'company_id'     => ['required', 'integer', 'exists:mst_company,id'],
            'parent_id'      => ['nullable', 'integer', 'exists:mst_function,id'],
            'code'           => ['required', 'string', 'max:255'],
            'name'           => ['required', 'string', 'max:255'],
            'alias'          => ['nullable', 'string', 'max:255'],
            'regulation_ids' => ['nullable', 'array'],
            'regulation_ids.*' => ['integer', 'exists:mst_regulation,id'],
        ]);

        $functionService->updateFunction($function, $validated);

        return redirect()->back()->with('success', 'Function berhasil diperbarui.');
    }

    /**
     * Remove the specified Function item.
     */
    public function destroyFunction(int $id, FunctionService $functionService): RedirectResponse
    {
        $function = MstFunction::findOrFail($id);
        $functionService->deleteFunction($function);

        return redirect()->back()->with('success', 'Function berhasil dihapus.');
    }
}

