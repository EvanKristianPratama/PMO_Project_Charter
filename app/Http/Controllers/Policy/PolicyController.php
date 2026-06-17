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
    /**
     * Display a listing of COBIT objectives and practices.
     */
    public function index(Request $request): Response
    {
        try {
            $regulations = \App\Models\MstRegulation::orderBy('id', 'desc')->get();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('[PolicyController] DB error loading regulations: ' . $e->getMessage());
            $regulations = collect([]);
        }

        $selectedRegulationId = $request->integer('regulation_id');
        $selectedRegulation = null;

        if ($selectedRegulationId) {
            $selectedRegulation = $regulations->firstWhere('id', $selectedRegulationId);
        }

        if (!$selectedRegulation) {
            // Prioritize regulation with "PEDOMAN TATA KELOLA" title
            $pedoman = $regulations->first(function ($r) {
                return str_contains(strtoupper($r->judul ?? ''), 'PEDOMAN TATA KELOLA');
            });
            $selectedRegulation = $pedoman ?? $regulations->first();
        }

        $objectives = MstObjective::with(['practices' => function($query) {
            $query->orderBy('practice_id', 'asc');
        }])
        ->where('regulation_id', $selectedRegulation?->id)
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

        return Inertia::render('Policy/Guidance/Specific/Index', [
            'objectives' => $objectives,
            'regulations' => $regulations,
            'selectedRegulationId' => $selectedRegulation?->id,
        ]);
    }

    /**
     * Display the specific policy management CRUD view.
     */
    public function manage(Request $request): Response
    {
        $regulations = \App\Models\MstRegulation::orderBy('judul', 'asc')->get();
        
        $selectedRegulationId = $request->integer('regulation_id');
        $selectedRegulation = null;

        if ($selectedRegulationId) {
            $selectedRegulation = $regulations->firstWhere('id', $selectedRegulationId);
        }

        if (!$selectedRegulation) {
            $selectedRegulation = $regulations->first();
        }

        $objectives = MstObjective::with(['practices' => function($query) {
            $query->orderBy('practice_id', 'asc');
        }])
        ->where('regulation_id', $selectedRegulation?->id)
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

        return Inertia::render('Policy/Guidance/Specific/Manage', [
            'objectives' => $objectives,
            'regulations' => $regulations,
            'selectedRegulationId' => $selectedRegulation?->id,
        ]);
    }

    /**
     * Store a newly created objective.
     */
    public function storeObjective(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'objective_id' => 'required|string|max:255|unique:mst_objective,objective_id',
            'regulation_id' => 'nullable|integer|exists:mst_regulation,id',
            'domain' => 'nullable|string|max:255',
            'objective' => 'required|string|max:255',
            'objective_description' => 'nullable|string',
            'objective_purpose' => 'nullable|string',
        ], [
            'objective_id.required' => 'Objective ID (e.g. EDM01) wajib diisi.',
            'objective_id.unique' => 'Objective ID sudah digunakan.',
            'objective.required' => 'Nama Objective wajib diisi.',
            'regulation_id.exists' => 'Regulasi tidak valid.',
        ]);

        MstObjective::create($validated);

        return redirect()
            ->route('policy.specific.manage', ['regulation_id' => $request->regulation_id])
            ->with('success', 'Governance Objective berhasil ditambahkan.');
    }

    /**
     * Update the specified objective.
     */
    public function updateObjective(Request $request, string $id): RedirectResponse
    {
        $objective = MstObjective::findOrFail($id);

        $validated = $request->validate([
            'regulation_id' => 'nullable|integer|exists:mst_regulation,id',
            'domain' => 'nullable|string|max:255',
            'objective' => 'required|string|max:255',
            'objective_description' => 'nullable|string',
            'objective_purpose' => 'nullable|string',
        ], [
            'objective.required' => 'Nama Objective wajib diisi.',
            'regulation_id.exists' => 'Regulasi tidak valid.',
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
                        'regulation_id' => $request->input('regulation_id'),
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
            ->route('policy.specific.manage', ['regulation_id' => $request->regulation_id])
            ->with('success', 'Governance Objective berhasil diperbarui.');
    }

    /**
     * Remove the specified objective.
     */
    public function destroyObjective(string $id): RedirectResponse
    {
        $objective = MstObjective::findOrFail($id);
        $regulationId = $objective->regulation_id;
        $objective->delete();

        return redirect()
            ->route('policy.specific.manage', ['regulation_id' => $regulationId])
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

        $objective = MstObjective::find($request->objective_id);
        $regulationId = $objective?->regulation_id;

        return redirect()
            ->route('policy.specific.manage', ['regulation_id' => $regulationId])
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

        $objective = MstObjective::find($practice->objective_id);
        $regulationId = $objective?->regulation_id;

        return redirect()
            ->route('policy.specific.manage', ['regulation_id' => $regulationId])
            ->with('success', 'Management Practice berhasil diperbarui.');
    }

    /**
     * Remove the specified practice.
     */
    public function destroyPractice(string $id): RedirectResponse
    {
        $practice = MstPractice::findOrFail($id);
        $objective = MstObjective::find($practice->objective_id);
        $regulationId = $objective?->regulation_id;
        $practice->delete();

        return redirect()
            ->route('policy.specific.manage', ['regulation_id' => $regulationId])
            ->with('success', 'Management Practice berhasil dihapus.');
    }
}
