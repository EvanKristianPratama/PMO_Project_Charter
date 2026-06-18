<?php

namespace App\Http\Controllers\Architecture\Function;

use App\Http\Controllers\Controller;
use App\Models\MstFunction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FunctionController extends Controller
{
    public function index()
    {
        $functions = MstFunction::orderBy('kode')->get();

        return Inertia::render('Architecture/Function/Index', [
            'functions' => $functions,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'parent_id' => ['nullable', 'integer', 'exists:mst_function,id'],
            'kode'      => ['required', 'string', 'max:255'],
            'name'      => ['required', 'string', 'max:255'],
        ]);

        MstFunction::create([
            'parent_id' => $validated['parent_id'] ?: null,
            'kode'      => $validated['kode'],
            'name'      => $validated['name'],
        ]);

        return redirect()->back()->with('success', 'Function berhasil ditambahkan.');
    }

    public function update(Request $request, int $id)
    {
        $function = MstFunction::findOrFail($id);

        $validated = $request->validate([
            'parent_id' => ['nullable', 'integer', 'exists:mst_function,id'],
            'kode'      => ['required', 'string', 'max:255'],
            'name'      => ['required', 'string', 'max:255'],
        ]);

        $function->update([
            'parent_id' => $validated['parent_id'] ?: null,
            'kode'      => $validated['kode'],
            'name'      => $validated['name'],
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
