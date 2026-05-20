<?php

namespace App\Http\Controllers\Policy;

use App\Http\Controllers\Controller;
use App\Models\MstRegulation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RegulationController extends Controller
{
    /**
     * Display a listing of regulations in read-only mode.
     */
    public function index(): Response
    {
        // Fetch regulations ordered by latest release or ID
        $regulations = MstRegulation::orderBy('id', 'desc')->get();

        return Inertia::render('Policy/Regulation/Index', [
            'regulations' => $regulations,
        ]);
    }

    /**
     * Display a listing of regulations for CRUD management.
     */
    public function manage(): Response
    {
        $regulations = MstRegulation::orderBy('id', 'desc')->get();

        return Inertia::render('Policy/Regulation/Manage', [
            'regulations' => $regulations,
        ]);
    }

    /**
     * Store a newly created regulation.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'tipe' => 'required|string|max:255',
            'owner' => 'required|string|max:255',
            'revisi' => 'required|string|max:255',
            'terbit' => 'nullable|date',
            'berlaku' => 'nullable|date',
        ], [
            'judul.required' => 'Judul Kebijakan wajib diisi.',
            'tipe.required' => 'Tipe Kebijakan wajib diisi.',
            'owner.required' => 'Owner Kebijakan wajib diisi.',
            'revisi.required' => 'Revisi Kebijakan wajib diisi.',
            'terbit.required' => 'Tanggal Terbit wajib diisi.',
            'terbit.date' => 'Tanggal Terbit harus berupa format tanggal.',
            'berlaku.required' => 'Tanggal Berlaku wajib diisi.',
            'berlaku.date' => 'Tanggal Berlaku harus berupa format tanggal.',
        ]);

        MstRegulation::create($validated);

        return redirect()
            ->route('policy.regulation.index')
            ->with('success', 'Regulasi Kebijakan berhasil ditambahkan.');
    }

    /**
     * Update the specified regulation.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $regulation = MstRegulation::findOrFail($id);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'tipe' => 'required|string|max:255',
            'owner' => 'required|string|max:255',
            'revisi' => 'required|string|max:255',
            'terbit' => 'nullable|date',
            'berlaku' => 'nullable|date',
        ], [
            'judul.required' => 'Judul Kebijakan wajib diisi.',
            'tipe.required' => 'Tipe Kebijakan wajib diisi.',
            'owner.required' => 'Owner Kebijakan wajib diisi.',
            'revisi.required' => 'Revisi Kebijakan wajib diisi.',
            'terbit.required' => 'Tanggal Terbit wajib diisi.',
            'terbit.date' => 'Tanggal Terbit harus berupa format tanggal.',
            'berlaku.required' => 'Tanggal Berlaku wajib diisi.',
            'berlaku.date' => 'Tanggal Berlaku harus berupa format tanggal.',
        ]);

        $regulation->update($validated);

        return redirect()
            ->route('policy.regulation.index')
            ->with('success', 'Regulasi Kebijakan berhasil diperbarui.');
    }

    /**
     * Remove the specified regulation.
     */
    public function destroy(int $id): RedirectResponse
    {
        $regulation = MstRegulation::findOrFail($id);
        $regulation->delete();

        return redirect()
            ->route('policy.regulation.index')
            ->with('success', 'Regulasi Kebijakan berhasil dihapus.');
    }
}
