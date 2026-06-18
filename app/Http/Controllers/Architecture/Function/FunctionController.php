<?php

namespace App\Http\Controllers\Architecture\Function;

use App\Http\Controllers\Controller;
use App\Models\MstFunction;
use App\Models\Groub;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FunctionController extends Controller
{
    public function index()
    {
        $functions = MstFunction::with(['groub', 'regulation'])->orderBy('code')->get();
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'groub_id'      => ['required', 'integer', 'exists:trs_groub,id'],
            'parent_id'     => ['nullable', 'integer', 'exists:mst_function,id'],
            'code'          => ['required', 'string', 'max:255'],
            'name'          => ['required', 'string', 'max:255'],
            'alias'         => ['nullable', 'string', 'max:255'],
            'regulation_id' => ['nullable', 'integer', 'exists:mst_regulation,id'],
        ]);

        MstFunction::create([
            'groub_id'      => $validated['groub_id'],
            'parent_id'     => $validated['parent_id'] ?: null,
            'code'          => $validated['code'],
            'name'          => $validated['name'],
            'alias'         => $validated['alias'] ?? null,
            'regulation_id' => $validated['regulation_id'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Function berhasil ditambahkan.');
    }

    public function update(Request $request, int $id)
    {
        $function = MstFunction::findOrFail($id);

        $validated = $request->validate([
            'groub_id'      => ['required', 'integer', 'exists:trs_groub,id'],
            'parent_id'     => ['nullable', 'integer', 'exists:mst_function,id'],
            'code'          => ['required', 'string', 'max:255'],
            'name'          => ['required', 'string', 'max:255'],
            'alias'         => ['nullable', 'string', 'max:255'],
            'regulation_id' => ['nullable', 'integer', 'exists:mst_regulation,id'],
        ]);

        $function->update([
            'groub_id'      => $validated['groub_id'],
            'parent_id'     => $validated['parent_id'] ?: null,
            'code'          => $validated['code'],
            'name'          => $validated['name'],
            'alias'         => $validated['alias'] ?? null,
            'regulation_id' => $validated['regulation_id'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Function berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $function = MstFunction::findOrFail($id);
        $function->delete();

        return redirect()->back()->with('success', 'Function berhasil dihapus.');
    }
}
