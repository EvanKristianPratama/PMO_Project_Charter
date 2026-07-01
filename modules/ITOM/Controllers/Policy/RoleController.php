<?php

namespace Modules\ITOM\Controllers\Policy;

use App\Http\Controllers\Controller;
use Modules\ITOM\Models\MstRole;
use Modules\ITOM\Models\TrsResponsibility;
use Modules\ITOM\Models\MstRegulation;
use Modules\ITOM\Models\MstResponsible;
use Modules\ITOM\Models\TrsResponsible;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RoleController extends Controller
{
    /**
     * Display a listing of roles & responsibilities in formal document view mode.
     */
    public function index(Request $request): Response
    {
        try {
            // Auto-seed default roles & responsibilities if the table is empty
            if (MstRole::count() === 0) {
                $board = MstRole::create([
                    'name' => 'Dewan Direksi (Board)',
                    'description' => 'Dewan Direksi adalah sekelompok eksekutif paling senior dan/atau direktur noneksekutif yang bertanggung jawab atas tata kelola dan kendali keseluruhan atas sumber daya Perusahaan.'
                ]);

                $boardResponsibilities = [
                    'Menetapkan IT Governance.',
                    'Menetapkan IT Strategy Definition.',
                    'Mengevaluasi dan mendapatkan pelaporan hasil pencapaian layanan TIK.',
                    'Memastikan monitor perkembangan teknologi.',
                    'Menetapkan Business Impact and Risk Analysis.',
                    'Menetapkan Risk Mitigation.',
                    'Menetapkan Risk Monitoring.',
                    'Menetapkan Pengelolaan sumber daya informasi.',
                    'Melakukan koordinasi strategi secara internal dengan pemangku kepentingan untuk memastikan keselarasan.',
                    'Menetapkan rencana perubahan dan pengelolaan stakeholders.'
                ];

                foreach ($boardResponsibilities as $content) {
                    TrsResponsibility::create([
                        'role_id' => $board->id,
                        'content' => $content
                    ]);
                }

                $execComm = MstRole::create([
                    'name' => 'Executive Committee',
                    'description' => 'Executive Committee adalah sekelompok eksekutif senior yang ditunjuk oleh dewan untuk memastikan bahwa dewan terlibat dan terus mendapat informasi tentang keputusan-keputusan penting.'
                ]);

                $execResponsibilities = [
                    'Mengelola IT Governance.',
                    'Mengelola IT Strategy Definition.',
                    'Mengevaluasi dan laporkan hasil pencapaian layanan TIK.',
                    'Memonitor perkembangan teknologi.',
                    'Menyusun Business Impact and Risk Analysis.',
                    'Melaksanakan Risk Mitigation.',
                    'Melaksanakan Risk Monitoring.',
                    'Mengelola sumber daya informasi.',
                    'Mengelola Business Process Implementation.',
                    'Melakukan koordinasi strategi secara internal dengan pemangku kepentingan untuk memastikan keselarasan.',
                    'Membangun rencana perubahan dan pengelolaan stakeholders.'
                ];

                foreach ($execResponsibilities as $content) {
                    TrsResponsibility::create([
                        'role_id' => $execComm->id,
                        'content' => $content
                    ]);
                }
            }

            $regulations = MstRegulation::orderBy('id', 'desc')->get();
            
            $selectedRegulationId = $request->integer('regulation_id');
            $selectedRegulation = null;
            if ($selectedRegulationId) {
                $selectedRegulation = $regulations->firstWhere('id', $selectedRegulationId);
            }
            
            if (!$selectedRegulation) {
                // Filter to prioritize regulation with "PEDOMAN TATA KELOLA" title
                $pedoman = $regulations->first(function ($r) {
                    return str_contains(strtoupper($r->judul ?? ''), 'PEDOMAN TATA KELOLA');
                });
                $selectedRegulation = $pedoman ?? $regulations->first();
            }

            if ($selectedRegulation) {
                $regulations = collect([$selectedRegulation])->merge(
                    $regulations->where('id', '!=', $selectedRegulation->id)->values()
                );
            }

            $roles = MstRole::with(['responsibilities' => function ($query) {
                $query->orderBy('id', 'asc');
            }, 'mappedResponsibles'])->orderBy('id', 'asc')->get();

            $responsibles = MstResponsible::with(['objectives' => function ($query) use ($selectedRegulation) {
                $query->where('regulation_id', $selectedRegulation?->id);
            }])->orderBy('responsible', 'asc')->get();

            $objectives = \Modules\ITOM\Models\MstObjective::with('practices')
                ->where('regulation_id', $selectedRegulation?->id)
                ->orderBy('objective_id', 'asc')
                ->get();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('[RoleController] DB error loading roles: ' . $e->getMessage());
            $roles = collect([]);
            $responsibles = collect([]);
            $regulations = collect([]);
            $objectives = collect([]);
            $selectedRegulation = null;
        }

        return Inertia::render('modules/ITOM/Policy/Guidance/Role/Index', [
            'roles' => $roles,
            'regulations' => $regulations,
            'responsibles' => $responsibles,
            'objectives' => $objectives,
            'selectedRegulationId' => $selectedRegulation?->id,
        ]);
    }

    /**
     * Display the roles & responsibilities management CRUD view.
     */
    public function manage(): Response
    {
        $roles = MstRole::with(['responsibilities' => function ($query) {
            $query->orderBy('id', 'asc');
        }, 'mappedResponsibles'])->orderBy('id', 'asc')->get();

        $responsibles = MstResponsible::with(['mappedRoles'])->orderBy('responsible', 'asc')->get();

        return Inertia::render('modules/ITOM/Policy/Guidance/Role/Manage', [
            'roles' => $roles,
            'responsibles' => $responsibles,
        ]);
    }

    /**
     * Store a newly created role.
     */
    public function storeRole(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Nama Role wajib diisi.',
        ]);

        MstRole::create($validated);

        return redirect()
            ->route('itom.policy.roles.manage')
            ->with('success', 'Role berhasil ditambahkan.');
    }

    /**
     * Update the specified role.
     */
    public function updateRole(Request $request, int $id): RedirectResponse
    {
        $role = MstRole::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Nama Role wajib diisi.',
        ]);

        $role->update($validated);

        return redirect()
            ->route('itom.policy.roles.manage')
            ->with('success', 'Role berhasil diperbarui.');
    }

    /**
     * Remove the specified role.
     */
    public function destroyRole(int $id): RedirectResponse
    {
        $role = MstRole::findOrFail($id);
        $role->delete();

        return redirect()
            ->route('itom.policy.roles.manage')
            ->with('success', 'Role berhasil dihapus.');
    }

    /**
     * Store a newly created responsibility.
     */
    public function storeResponsibility(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'role_id' => 'required|integer|exists:mst_roles,id',
            'content' => 'required|string',
        ], [
            'role_id.required' => 'Role wajib dipilih.',
            'role_id.exists' => 'Role tidak valid.',
            'content.required' => 'Isi Tanggung Jawab / Responsibility wajib diisi.',
        ]);

        TrsResponsibility::create($validated);

        return redirect()
            ->route('itom.policy.roles.manage')
            ->with('success', 'Responsibility berhasil ditambahkan.');
    }

    /**
     * Update the specified responsibility.
     */
    public function updateResponsibility(Request $request, int $id): RedirectResponse
    {
        $responsibility = TrsResponsibility::findOrFail($id);

        $validated = $request->validate([
            'content' => 'required|string',
        ], [
            'content.required' => 'Isi Tanggung Jawab / Responsibility wajib diisi.',
        ]);

        $responsibility->update($validated);

        return redirect()
            ->route('itom.policy.roles.manage')
            ->with('success', 'Responsibility berhasil diperbarui.');
    }

    /**
     * Remove the specified responsibility.
     */
    public function destroyResponsibility(int $id): RedirectResponse
    {
        $responsibility = TrsResponsibility::findOrFail($id);
        $responsibility->delete();

        return redirect()
            ->route('itom.policy.roles.manage')
            ->with('success', 'Responsibility berhasil dihapus.');
    }

    /**
     * Store a newly created role mapping to a master responsible.
     */
    public function storeMappedResponsible(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'role_id' => 'required|integer|exists:mst_roles,id',
            'responsible_id' => 'nullable|integer',
            'responsible_ids' => 'nullable|array',
            'responsible_ids.*' => 'integer',
        ], [
            'role_id.required' => 'Role wajib dipilih.',
            'role_id.exists' => 'Role tidak valid.',
            'responsible_ids.array' => 'Daftar Master Responsible tidak valid.',
        ]);

        $ids = [];
        if (!empty($validated['responsible_ids'])) {
            $ids = $validated['responsible_ids'];
        } elseif (!empty($validated['responsible_id'])) {
            $ids = [$validated['responsible_id']];
        }

        if (empty($ids)) {
            return redirect()
                ->route('itom.policy.roles.manage')
                ->with('error', 'Silakan pilih minimal satu Master Responsible.');
        }

        // Bulk validate exists to avoid N+1 queries on remote DB
        $uniqueIds = array_unique($ids);
        $validResponsiblesCount = \DB::table('mst_responsible')
            ->whereIn('id', $uniqueIds)
            ->count();
        if ($validResponsiblesCount !== count($uniqueIds)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'responsible_id' => ['Master Responsible tidak valid.']
            ]);
        }

        // Check which IDs are already mapped to prevent duplicate entries
        $existingIds = \DB::table('trs_responsible')
            ->where('role_id', $validated['role_id'])
            ->whereIn('responsible_id', $ids)
            ->pluck('responsible_id')
            ->toArray();

        $newIds = array_diff($ids, $existingIds);

        if (empty($newIds)) {
            return redirect()
                ->route('itom.policy.roles.manage')
                ->with('error', 'Responsible yang dipilih sudah dipetakan pada role ini.');
        }

        // Attach relationship
        $role = MstRole::findOrFail($validated['role_id']);
        $role->mappedResponsibles()->attach($newIds);

        $count = count($newIds);
        return redirect()
            ->route('itom.policy.roles.manage')
            ->with('success', "{$count} Pemetaan Master Responsible berhasil ditambahkan.");
    }

    /**
     * Remove the specified role mapping from a master responsible.
     */
    public function destroyMappedResponsible(int $roleId, int $responsibleId): RedirectResponse
    {
        $role = MstRole::findOrFail($roleId);
        $role->mappedResponsibles()->detach($responsibleId);

        return redirect()
            ->route('itom.policy.roles.manage')
            ->with('success', 'Pemetaan Master Responsible berhasil dihapus.');
    }

    /**
     * Update mapping between responsibilities (Bab 3) and policies/objectives (Bab 2).
     */
    public function updateResponsiblePractice(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mappings' => 'required|array',
            'mappings.*.responsible_id' => 'required|integer',
            'mappings.*.objective_ids' => 'present|array',
            'mappings.*.objective_ids.*' => 'string',
        ]);

        // Bulk validate to avoid N+1 exists queries on remote DB
        $mappings = $validated['mappings'];
        $responsibleIds = [];
        $objectiveIds = [];

        foreach ($mappings as $map) {
            $responsibleIds[] = $map['responsible_id'];
            if (!empty($map['objective_ids'])) {
                foreach ($map['objective_ids'] as $objId) {
                    $objectiveIds[] = $objId;
                }
            }
        }

        $responsibleIds = array_unique($responsibleIds);
        $objectiveIds = array_unique($objectiveIds);

        if (!empty($responsibleIds)) {
            $validResponsiblesCount = \DB::table('mst_responsible')
                ->whereIn('id', $responsibleIds)
                ->count();
            if ($validResponsiblesCount !== count($responsibleIds)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'mappings' => ['Beberapa ID Master Responsible tidak valid.']
                ]);
            }
        }

        if (!empty($objectiveIds)) {
            $validObjectivesCount = \DB::table('mst_objective')
                ->whereIn('objective_id', $objectiveIds)
                ->count();
            if ($validObjectivesCount !== count($objectiveIds)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'mappings' => ['Beberapa ID Kebijakan tidak valid.']
                ]);
            }
        }

        try {
            \DB::transaction(function () use ($validated) {
                $mappings = collect($validated['mappings']);
                $responsibleIds = $mappings->pluck('responsible_id')->toArray();

                if (empty($responsibleIds)) {
                    return;
                }

                // 1. Bulk delete existing mappings for all submitted responsibilities
                \DB::table('trs_responsible_objective')
                    ->whereIn('responsible_id', $responsibleIds)
                    ->delete();

                // 2. Prepare bulk insert payload
                $insertData = [];
                $now = now();
                
                foreach ($mappings as $map) {
                    if (!empty($map['objective_ids'])) {
                        foreach ($map['objective_ids'] as $objId) {
                            $insertData[] = [
                                'responsible_id' => $map['responsible_id'],
                                'objective_id' => $objId,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ];
                        }
                    }
                }

                // 3. Bulk insert in chunks to avoid query length limits
                if (!empty($insertData)) {
                    foreach (array_chunk($insertData, 500) as $chunk) {
                        \DB::table('trs_responsible_objective')->insert($chunk);
                    }
                }
            });

            return redirect()
                ->route('itom.policy.roles.index')
                ->with('success', 'Pemetaan Tanggung Jawab vs Kebijakan berhasil diperbarui.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('[RoleController] Error updating responsible objective mapping: ' . $e->getMessage());
            return redirect()
                ->route('itom.policy.roles.index')
                ->with('error', 'Gagal memperbarui pemetaan Tanggung Jawab vs Kebijakan.');
        }
    }

    /**
     * Update mapping of responsibilities for a single objective (row-by-row).
     */
    public function updateObjectiveResponsibles(Request $request, string $objectiveId): RedirectResponse
    {
        $validated = $request->validate([
            'responsible_ids' => 'present|array',
            'responsible_ids.*' => 'integer',
        ]);

        $ids = $validated['responsible_ids'] ?? [];
        if (!empty($ids)) {
            $uniqueIds = array_unique($ids);
            $validResponsiblesCount = \DB::table('mst_responsible')
                ->whereIn('id', $uniqueIds)
                ->count();
            if ($validResponsiblesCount !== count($uniqueIds)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'responsible_ids' => ['Beberapa ID Master Responsible tidak valid.']
                ]);
            }
        }

        try {
            $objective = \Modules\ITOM\Models\MstObjective::findOrFail($objectiveId);
            $objective->responsibles()->sync($validated['responsible_ids']);

            return redirect()
                ->route('itom.policy.roles.index')
                ->with('success', 'Pemetaan Tanggung Jawab berhasil diperbarui.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('[RoleController] Error updating single objective responsibles mapping: ' . $e->getMessage());
            return redirect()
                ->route('itom.policy.roles.index')
                ->with('error', 'Gagal memperbarui pemetaan Tanggung Jawab.');
        }
    }
}
