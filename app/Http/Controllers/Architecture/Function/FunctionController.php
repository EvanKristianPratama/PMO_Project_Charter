<?php

namespace App\Http\Controllers\Architecture\Function;

use App\Http\Controllers\Controller;
use App\Models\MstFunction;
use App\Models\Groub;
use App\Services\Architecture\FunctionService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FunctionController extends Controller
{
    public function index(FunctionService $functionService)
    {
        $functions = $functionService->getFunctions();
        $groubOptions = Groub::with('company')->orderBy('name')->get()->map(fn ($g) => [
            'id' => $g->id,
            'name' => $g->company ? "{$g->company->name} - {$g->name}" : $g->name,
        ])->values()->all();
        $regulations = \App\Models\MstRegulation::orderBy('judul')->get()->map(fn ($r) => [
            'id' => $r->id,
            'judul' => $r->judul,
            'nomor' => $r->nomor,
        ])->values()->all();

        return Inertia::render('Architecture/Function/Index', [
            'functions' => $functions,
            'groubOptions' => $groubOptions,
            'regulations' => $regulations,
        ]);
    }

    public function store(Request $request, FunctionService $functionService)
    {
        $validated = $request->validate([
            'groub_id'       => ['required', 'integer', 'exists:trs_groub,id'],
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

    public function update(Request $request, int $id, FunctionService $functionService)
    {
        $function = MstFunction::findOrFail($id);

        $validated = $request->validate([
            'groub_id'       => ['required', 'integer', 'exists:trs_groub,id'],
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

    public function destroy(int $id, FunctionService $functionService)
    {
        $function = MstFunction::findOrFail($id);
        $functionService->deleteFunction($function);

        return redirect()->back()->with('success', 'Function berhasil dihapus.');
    }
}

