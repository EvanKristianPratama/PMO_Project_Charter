<?php

namespace Modules\ITOM\Services\Regulation;

use Modules\ITOM\Models\MstRole;
use Modules\ITOM\Models\TrsResponsibility;
use Modules\ITOM\Models\MstRegulation;
use Modules\ITOM\Models\MstResponsible;
use Modules\ITOM\Models\MstObjective;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoleService
{
    protected static $cachedData = [];

    /**
     * Get all data for the role index (with auto-seeding).
     *
     * @param int|null $selectedRegulationId
     * @return array
     */
    public function getRoleIndexData(?int $selectedRegulationId): array
    {
        if (isset(self::$cachedData[$selectedRegulationId])) {
            return self::$cachedData[$selectedRegulationId];
        }

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
            
            $selectedRegulation = null;
            if ($selectedRegulationId) {
                $selectedRegulation = $regulations->firstWhere('id', $selectedRegulationId);
            }
            
            if (!$selectedRegulation) {
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

            $objectives = MstObjective::with('practices')
                ->where('regulation_id', $selectedRegulation?->id)
                ->orderBy('objective_id', 'asc')
                ->get();
        } catch (\Exception $e) {
            Log::warning('[RoleService] DB error loading roles: ' . $e->getMessage());
            $roles = collect([]);
            $responsibles = collect([]);
            $regulations = collect([]);
            $objectives = collect([]);
            $selectedRegulation = null;
        }

        self::$cachedData[$selectedRegulationId] = [
            'roles' => $roles,
            'regulations' => $regulations,
            'responsibles' => $responsibles,
            'objectives' => $objectives,
            'selectedRegulationId' => $selectedRegulation?->id,
        ];

        return self::$cachedData[$selectedRegulationId];
    }

    /**
     * Get all data for the role management page.
     *
     * @param int|null $selectedRegulationId
     * @return array
     */
    public function getRoleManageData(?int $selectedRegulationId = null): array
    {
        $roles = MstRole::with(['responsibilities' => function ($query) {
            $query->orderBy('id', 'asc');
        }, 'mappedResponsibles'])->orderBy('id', 'asc')->get();

        $responsibles = MstResponsible::with(['mappedRoles'])->orderBy('responsible', 'asc')->get();

        $regulations = MstRegulation::orderBy('id', 'desc')->get();
        $selectedRegulation = null;
        if ($selectedRegulationId) {
            $selectedRegulation = $regulations->firstWhere('id', $selectedRegulationId);
        }
        if (!$selectedRegulation && $regulations->isNotEmpty()) {
            $selectedRegulation = $regulations->first();
        }

        return [
            'roles' => $roles,
            'responsibles' => $responsibles,
            'regulations' => $regulations,
            'selectedRegulationId' => $selectedRegulation?->id,
        ];
    }

    /**
     * Create a new role.
     *
     * @param array $data
     * @return MstRole
     */
    public function createRole(array $data): MstRole
    {
        return MstRole::create($data);
    }

    /**
     * Update the specified role.
     *
     * @param MstRole $role
     * @param array $data
     * @return MstRole
     */
    public function updateRole(MstRole $role, array $data): MstRole
    {
        $role->update($data);
        return $role;
    }

    /**
     * Delete the specified role.
     *
     * @param MstRole $role
     * @return void
     */
    public function deleteRole(MstRole $role): void
    {
        $role->delete();
    }

    /**
     * Create a new responsibility.
     *
     * @param array $data
     * @return TrsResponsibility
     */
    public function createResponsibility(array $data): TrsResponsibility
    {
        return TrsResponsibility::create($data);
    }

    /**
     * Update the specified responsibility.
     *
     * @param TrsResponsibility $responsibility
     * @param array $data
     * @return TrsResponsibility
     */
    public function updateResponsibility(TrsResponsibility $responsibility, array $data): TrsResponsibility
    {
        $responsibility->update($data);
        return $responsibility;
    }

    /**
     * Delete the specified responsibility.
     *
     * @param TrsResponsibility $responsibility
     * @return void
     */
    public function deleteResponsibility(TrsResponsibility $responsibility): void
    {
        $responsibility->delete();
    }

    /**
     * Store mapping of responsibles to a role.
     *
     * @param MstRole $role
     * @param array $ids
     * @return array [success: bool, message: string]
     */
    public function storeMappedResponsible(MstRole $role, array $ids): array
    {
        // Bulk validate exists to avoid N+1 queries on remote DB
        $uniqueIds = array_unique($ids);
        $validResponsiblesCount = DB::table('mst_responsible')
            ->whereIn('id', $uniqueIds)
            ->count();
        if ($validResponsiblesCount !== count($uniqueIds)) {
            return [
                'success' => false,
                'message' => 'Master Responsible tidak valid.',
            ];
        }

        // Check which IDs are already mapped to prevent duplicate entries
        $existingIds = DB::table('trs_responsible')
            ->where('role_id', $role->id)
            ->whereIn('responsible_id', $ids)
            ->pluck('responsible_id')
            ->toArray();

        $newIds = array_diff($ids, $existingIds);

        if (empty($newIds)) {
            return [
                'success' => false,
                'message' => 'Responsible yang dipilih sudah dipetakan pada role ini.',
            ];
        }

        // Attach relationship
        $role->mappedResponsibles()->attach($newIds);

        $count = count($newIds);
        return [
            'success' => true,
            'message' => "{$count} Pemetaan Master Responsible berhasil ditambahkan.",
        ];
    }

    /**
     * Remove role mapping from a master responsible.
     *
     * @param MstRole $role
     * @param int $responsibleId
     * @return void
     */
    public function destroyMappedResponsible(MstRole $role, int $responsibleId): void
    {
        $role->mappedResponsibles()->detach($responsibleId);
    }

    /**
     * Bulk update mapping of responsibilities to objectives.
     *
     * @param array $mappings
     * @return void
     */
    public function updateResponsiblePractice(array $mappings): void
    {
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
            $validResponsiblesCount = DB::table('mst_responsible')
                ->whereIn('id', $responsibleIds)
                ->count();
            if ($validResponsiblesCount !== count($responsibleIds)) {
                throw new \Exception('Beberapa ID Master Responsible tidak valid.');
            }
        }

        if (!empty($objectiveIds)) {
            $validObjectivesCount = DB::table('mst_objective')
                ->whereIn('objective_id', $objectiveIds)
                ->count();
            if ($validObjectivesCount !== count($objectiveIds)) {
                throw new \Exception('Beberapa ID Kebijakan tidak valid.');
            }
        }

        DB::transaction(function () use ($mappings, $responsibleIds) {
            if (empty($responsibleIds)) {
                return;
            }

            // 1. Bulk delete existing mappings for all submitted responsibilities
            DB::table('trs_responsible_objective')
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
                    DB::table('trs_responsible_objective')->insert($chunk);
                }
            }
        });
    }

    /**
     * Update mapping of responsibilities for a single objective.
     *
     * @param string $objectiveId
     * @param array $responsibleIds
     * @return void
     */
    public function updateObjectiveResponsibles(string $objectiveId, array $responsibleIds): void
    {
        if (!empty($responsibleIds)) {
            $uniqueIds = array_unique($responsibleIds);
            $validResponsiblesCount = DB::table('mst_responsible')
                ->whereIn('id', $uniqueIds)
                ->count();
            if ($validResponsiblesCount !== count($uniqueIds)) {
                throw new \Exception('Beberapa ID Master Responsible tidak valid.');
            }
        }

        $objective = MstObjective::findOrFail($objectiveId);
        $objective->responsibles()->sync($responsibleIds);
    }
}
