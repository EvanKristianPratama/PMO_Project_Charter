<?php

namespace App\Http\Controllers\Policy;

use App\Http\Controllers\Controller;
use App\Models\MstObjective;
use App\Models\MstPractice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PolicyController extends Controller
{
    /**
     * Display a listing of COBIT objectives and practices.
     */
    public function index(): Response
    {
        $objectives = MstObjective::with('practices')
            ->orderBy('objective_id')
            ->get();

        return Inertia::render('Policy/Index', [
            'objectives' => $objectives,
        ]);
    }

    /**
     * Store a newly created objective.
     */
    public function storeObjective(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'objective_id' => 'required|string|max:255|unique:mst_objective,objective_id',
            'domain' => 'nullable|string|max:255',
            'objective' => 'required|string|max:255',
            'objective_description' => 'nullable|string',
            'objective_purpose' => 'nullable|string',
        ], [
            'objective_id.required' => 'Objective ID (e.g. EDM01) wajib diisi.',
            'objective_id.unique' => 'Objective ID sudah digunakan.',
            'objective.required' => 'Nama Objective wajib diisi.',
        ]);

        MstObjective::create($validated);

        return redirect()
            ->route('policy.index')
            ->with('success', 'Governance Objective berhasil ditambahkan.');
    }

    /**
     * Update the specified objective.
     */
    public function updateObjective(Request $request, string $id): RedirectResponse
    {
        $objective = MstObjective::findOrFail($id);

        $validated = $request->validate([
            'domain' => 'nullable|string|max:255',
            'objective' => 'required|string|max:255',
            'objective_description' => 'nullable|string',
            'objective_purpose' => 'nullable|string',
        ], [
            'objective.required' => 'Nama Objective wajib diisi.',
        ]);

        $objective->update($validated);

        return redirect()
            ->route('policy.index')
            ->with('success', 'Governance Objective berhasil diperbarui.');
    }

    /**
     * Remove the specified objective.
     */
    public function destroyObjective(string $id): RedirectResponse
    {
        $objective = MstObjective::findOrFail($id);
        $objective->delete();

        return redirect()
            ->route('policy.index')
            ->with('success', 'Governance Objective berhasil dihapus.');
    }

    /**
     * Store a newly created practice.
     */
    public function storePractice(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'practice_id' => 'required|string|max:255|unique:mst_practice,practice_id',
            'objective_id' => 'required|string|exists:mst_objective,objective_id',
            'practice_name' => 'nullable|string|max:255',
            'practice_description' => 'nullable|string',
        ], [
            'practice_id.required' => 'Practice ID (e.g. EDM01.01) wajib diisi.',
            'practice_id.unique' => 'Practice ID sudah digunakan.',
            'objective_id.exists' => 'Objective ID tidak valid.',
        ]);

        MstPractice::create($validated);

        return redirect()
            ->route('policy.index')
            ->with('success', 'Management Practice berhasil ditambahkan.');
    }

    /**
     * Update the specified practice.
     */
    public function updatePractice(Request $request, string $id): RedirectResponse
    {
        $practice = MstPractice::findOrFail($id);

        $validated = $request->validate([
            'practice_name' => 'nullable|string|max:255',
            'practice_description' => 'nullable|string',
        ]);

        $practice->update($validated);

        return redirect()
            ->route('policy.index')
            ->with('success', 'Management Practice berhasil diperbarui.');
    }

    /**
     * Remove the specified practice.
     */
    public function destroyPractice(string $id): RedirectResponse
    {
        $practice = MstPractice::findOrFail($id);
        $practice->delete();

        return redirect()
            ->route('policy.index')
            ->with('success', 'Management Practice berhasil dihapus.');
    }
}
