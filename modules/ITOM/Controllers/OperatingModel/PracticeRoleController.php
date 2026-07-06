<?php

namespace Modules\ITOM\Controllers\OperatingModel;

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
use Illuminate\Support\Facades\Log;

class PracticeRoleController extends Controller
{
    /**
     * Display the RACI Matrix.
     */
    public function index(): Response
    {
        return Inertia::render('modules/ITOM/OperatingModel/RaciAnalysis/Index', [
            'objectives' => Inertia::defer(fn() => MstObjective::select(['objective_id', 'objective'])
                ->with(['practices' => function($query) {
                    $query->select(['practice_id', 'objective_id', 'practice_name', 'practice_description'])
                        ->orderBy('practice_id', 'asc');
                }])
                ->orderBy('objective_id', 'asc')
                ->get()
            ),
            'roles' => Inertia::defer(fn() => MstRole::select(['id', 'name'])->orderBy('id', 'asc')->get()),
            'mappings' => Inertia::defer(fn() => TrsPracticeRole::select(['practice_id', 'role_id', 'r_a'])->get()),
        ]);
    }

    /**
     * Display the RACI Matrix input/mapping management view.
     */
    public function manage(): Response
    {
        return Inertia::render('modules/ITOM/OperatingModel/RaciAnalysis/Manage', [
            'objectives' => Inertia::defer(fn() => MstObjective::select(['objective_id', 'objective'])
                ->with(['practices' => function($query) {
                    $query->select(['practice_id', 'objective_id', 'practice_name', 'practice_description'])
                        ->orderBy('practice_id', 'asc');
                }])
                ->orderBy('objective_id', 'asc')
                ->get()
            ),
            'roles' => Inertia::defer(fn() => MstRole::select(['id', 'name'])->orderBy('id', 'asc')->get()),
            'mappings' => Inertia::defer(fn() => TrsPracticeRole::select(['practice_id', 'role_id', 'r_a'])->get()),
        ]);
    }

    /**
     * Save the bulk RACI mapping modifications.
     * Optimized: Frontend only sends changed/dirty items, not the entire grid.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'mappings' => 'required|array',
            'mappings.*.practice_id' => 'required|string',
            'mappings.*.role_id' => 'required|integer',
            'mappings.*.r_a' => 'nullable|string|in:R,A,C,I,r,a,c,i,',
        ]);

        $mappings = $request->input('mappings', []);

        // Extend execution time for bulk operations on remote cloud DB
        set_time_limit(120);

        Log::info('RACI update: received ' . count($mappings) . ' mapping items');

        if (empty($mappings)) {
            return redirect()
                ->back()
                ->with('success', 'Tidak ada perubahan.');
        }

        // Separate into upserts vs deletes
        $toUpsert = [];
        $toDelete = [];

        foreach ($mappings as $item) {
            $raciVal = !empty($item['r_a']) ? strtoupper(trim($item['r_a'])) : null;

            if ($raciVal === null) {
                $toDelete[] = $item;
            } else {
                $toUpsert[] = [
                    'practice_id' => $item['practice_id'],
                    'role_id' => $item['role_id'],
                    'r_a' => $raciVal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::transaction(function () use ($toUpsert, $toDelete) {
            // Bulk delete cleared mappings
            foreach ($toDelete as $item) {
                TrsPracticeRole::where('practice_id', $item['practice_id'])
                    ->where('role_id', $item['role_id'])
                    ->delete();
            }

            // Bulk upsert in chunks of 500 to avoid oversized SQL statements
            foreach (array_chunk($toUpsert, 500) as $chunk) {
                TrsPracticeRole::upsert(
                    $chunk,
                    ['practice_id', 'role_id'],
                    ['r_a', 'updated_at']
                );
            }
        });

        return redirect()
            ->back()
            ->with('success', 'Matriks RACI berhasil diperbarui.');
    }
}
