<?php

namespace Modules\ITOM\Controllers\Regulation\CMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\MstDocument;

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
}