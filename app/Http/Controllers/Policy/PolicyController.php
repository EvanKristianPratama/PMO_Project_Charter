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
        $objectives = MstObjective::with(['practices' => function($query) {
            $query->orderBy('practice_id', 'asc');
        }])
        ->orderByRaw("
            CASE 
                WHEN objective_id LIKE 'EDM%' THEN 1
                WHEN objective_id LIKE 'APO%' THEN 2
                WHEN objective_id LIKE 'BAI%' THEN 3
                WHEN objective_id LIKE 'DSS%' THEN 4
                WHEN objective_id LIKE 'MEA%' THEN 5
                ELSE 6
            END ASC
        ")
        ->orderBy('objective_id', 'asc')
        ->get();

        return Inertia::render('Policy/Specific/Index', [
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
            ->route('policy.specific.index')
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

        if ($request->filled('objective_id') && $request->input('objective_id') !== $id) {
            $newId = $request->input('objective_id');
            $request->validate([
                'objective_id' => 'required|string|max:255|unique:mst_objective,objective_id',
            ], [
                'objective_id.unique' => 'Objective ID sudah digunakan.',
                'objective_id.required' => 'Objective ID wajib diisi.',
            ]);

            \DB::transaction(function() use ($id, $newId, $request) {
                \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                
                \DB::table('mst_objective')
                    ->where('objective_id', $id)
                    ->update([
                        'objective_id' => $newId,
                        'domain' => $request->input('domain'),
                        'objective' => $request->input('objective'),
                        'objective_description' => $request->input('objective_description'),
                        'objective_purpose' => $request->input('objective_purpose'),
                    ]);
                    
                \DB::table('mst_practice')
                    ->where('objective_id', $id)
                    ->update(['objective_id' => $newId]);
                    
                \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            });
        } else {
            $objective->update($validated);
        }

        return redirect()
            ->route('policy.specific.index')
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
            ->route('policy.specific.index')
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
            ->route('policy.specific.index')
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

        if ($request->filled('practice_id') && $request->input('practice_id') !== $id) {
            $newId = $request->input('practice_id');
            $request->validate([
                'practice_id' => 'required|string|max:255|unique:mst_practice,practice_id',
            ], [
                'practice_id.unique' => 'Practice ID sudah digunakan.',
                'practice_id.required' => 'Practice ID wajib diisi.',
            ]);

            \DB::table('mst_practice')
                ->where('practice_id', $id)
                ->update([
                    'practice_id' => $newId,
                    'practice_name' => $request->input('practice_name'),
                    'practice_description' => $request->input('practice_description'),
                ]);
        } else {
            $practice->update($validated);
        }

        return redirect()
            ->route('policy.specific.index')
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
            ->route('policy.specific.index')
            ->with('success', 'Management Practice berhasil dihapus.');
    }
}
