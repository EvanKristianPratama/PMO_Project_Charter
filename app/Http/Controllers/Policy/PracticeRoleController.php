<?php

namespace App\Http\Controllers\Policy;

use App\Http\Controllers\Controller;
use App\Models\MstObjective;
use App\Models\MstPractice;
use App\Models\MstRole;
use App\Models\TrsPracticeRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\DB;

class PracticeRoleController extends Controller
{
    /**
     * Display the RACI Matrix.
     */
    public function index(): Response
    {
        $objectives = MstObjective::with(['practices' => function($query) {
            $query->orderBy('practice_id', 'asc');
        }, 'practices.roles'])
        ->orderBy('objective_id', 'asc')
        ->get();

        $roles = MstRole::orderBy('id', 'asc')->get();

        // Load all direct mappings to make frontend lookups super simple
        $mappings = TrsPracticeRole::all();

        return Inertia::render('Policy/Raci/Index', [
            'objectives' => $objectives,
            'roles' => $roles,
            'mappings' => $mappings,
        ]);
    }

    /**
     * Display the RACI Matrix input/mapping management view.
     */
    public function manage(): Response
    {
        $objectives = MstObjective::with(['practices' => function($query) {
            $query->orderBy('practice_id', 'asc');
        }])
        ->orderBy('objective_id', 'asc')
        ->get();

        $roles = MstRole::orderBy('id', 'asc')->get();
        
        $mappings = TrsPracticeRole::all();

        return Inertia::render('Policy/Raci/Manage', [
            'objectives' => $objectives,
            'roles' => $roles,
            'mappings' => $mappings,
        ]);
    }

    /**
     * Save the bulk RACI mapping modifications.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'mappings' => 'required|array',
            'mappings.*.practice_id' => 'required|string|exists:mst_practice,practice_id',
            'mappings.*.role_id' => 'required|integer|exists:mst_roles,id',
            'mappings.*.r_a' => 'nullable|string|in:R,A,C,I,r,a,c,i,', // empty string also allowed to clear
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->input('mappings') as $item) {
                $raciVal = !empty($item['r_a']) ? strtoupper(trim($item['r_a'])) : null;

                if ($raciVal === null) {
                    TrsPracticeRole::where('practice_id', $item['practice_id'])
                        ->where('role_id', $item['role_id'])
                        ->delete();
                } else {
                    TrsPracticeRole::updateOrCreate(
                        [
                            'practice_id' => $item['practice_id'],
                            'role_id' => $item['role_id'],
                        ],
                        [
                            'r_a' => $raciVal,
                        ]
                    );
                }
            }
        });

        return redirect()
            ->route('policy.raci.index')
            ->with('success', 'Matriks RACI berhasil diperbarui.');
    }
}
