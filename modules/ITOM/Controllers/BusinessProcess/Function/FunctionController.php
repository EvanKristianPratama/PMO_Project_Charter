<?php

namespace Modules\ITOM\Controllers\BusinessProcess\Function;

use App\Http\Controllers\Controller;
use App\Models\MstFunction;
use App\Services\BusinessProcess\FunctionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FunctionController extends Controller
{
    /**
     * Display a listing of Function items.
     */
    public function index(FunctionService $functionService): Response
    {
        return Inertia::render('modules/ITOM/BusinessProcess/Function/Index', [
            'functions' => Inertia::defer(fn() => $functionService->getFunctions()),
            'companyOptions' => Inertia::defer(fn() => \App\Models\MstCompany::orderBy('name')->get(['id', 'name'])),
            'bodOptions' => Inertia::defer(fn() => \App\Models\MstBod::orderBy('order')->orderBy('name')->get(['id', 'name', 'alias', 'parent_id', 'order', 'pejabat', 'tipe'])),
            'regulations' => Inertia::defer(fn() => \App\Models\MstRegulation::orderBy('judul')->get(['id', 'judul', 'nomor', 'tipe', 'parent_id', 'status'])),
        ]);
    }

    /**
     * Store a newly created Function item.
     */
    public function storeFunction(Request $request, FunctionService $functionService): RedirectResponse
    {
        $validated = $request->validate([
            'company_id'     => ['required', 'integer', 'exists:mst_company,id'],
            'parent_id'      => ['nullable', 'integer', 'exists:mst_function,id'],
            'name'           => ['required', 'string', 'max:255'],
            'alias'          => ['nullable', 'string', 'max:255'],
            'deskripsi'      => ['nullable', 'string'],
            'regulation_ids' => ['nullable', 'array'],
            'regulation_ids.*' => ['integer', 'exists:mst_regulation,id'],
            'organization_ids' => ['nullable', 'array'],
            'organization_ids.*' => ['integer', 'exists:mst_bod,id'],
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
            'name'           => ['required', 'string', 'max:255'],
            'alias'          => ['nullable', 'string', 'max:255'],
            'deskripsi'      => ['nullable', 'string'],
            'regulation_ids' => ['nullable', 'array'],
            'regulation_ids.*' => ['integer', 'exists:mst_regulation,id'],
            'organization_ids' => ['nullable', 'array'],
            'organization_ids.*' => ['integer', 'exists:mst_bod,id'],
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
