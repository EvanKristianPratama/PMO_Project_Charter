<?php

namespace App\Services\Regulation;

use App\Models\MstCompany;
use App\Models\MstRegulation;
use App\Models\MstBod;
use App\Models\TrsOrganization;
use App\Models\MstActor;
use App\Models\TrsSopCategory;
use App\Models\MstSop;
use App\Models\TrsTkoSections;
use App\Models\MstObjective;
use App\Models\MstRole;

class RegulationService
{
    /**
     * Retrieve all data needed for regulation index.
     *
     * @return array
     */
    public function getIndexData(): array
    {
        $regulations = MstRegulation::with(['parent', 'revokedRegulations', 'relatedRegulations', 'mstBod.company', 'mstCompany'])
            ->withCount(['generalPolicies'])
            ->orderBy('id', 'asc')
            ->get();
        $organizations = TrsOrganization::all();
        $companies = MstCompany::orderBy('name')->get();
        $bods = MstBod::orderBy('order')->orderBy('name')->get();

        return [
            'regulations' => $regulations,
            'organizations' => $organizations,
            'companies' => $companies,
            'bods' => $bods,
        ];
    }

    /**
     * Store a newly created regulation.
     *
     * @param array $data
     * @return MstRegulation
     */
    public function create(array $data): MstRegulation
    {
        $regulation = MstRegulation::create(\Illuminate\Support\Arr::except($data, ['revoked_ids', 'related_ids']));

        if (isset($data['revoked_ids'])) {
            $regulation->revokedRegulations()->sync($data['revoked_ids']);
        }

        if (isset($data['related_ids'])) {
            $regulation->relatedRegulations()->sync($data['related_ids']);
        }

        return $regulation;
    }

    /**
     * Update the specified regulation.
     *
     * @param MstRegulation $regulation
     * @param array $data
     * @return MstRegulation
     */
    public function update(MstRegulation $regulation, array $data): MstRegulation
    {
        $regulation->update(\Illuminate\Support\Arr::except($data, ['revoked_ids', 'related_ids']));

        if (isset($data['revoked_ids'])) {
            $regulation->revokedRegulations()->sync($data['revoked_ids']);
        } else {
            $regulation->revokedRegulations()->detach();
        }

        if (isset($data['related_ids'])) {
            $regulation->relatedRegulations()->sync($data['related_ids']);
        } else {
            $regulation->relatedRegulations()->detach();
        }

        return $regulation->refresh();
    }

    /**
     * Remove the specified regulation.
     *
     * @param MstRegulation $regulation
     * @return void
     */
    public function delete(MstRegulation $regulation): void
    {
        $regulation->delete();
    }

    /**
     * Get preview data for a specific regulation.
     *
     * @param MstRegulation $regulation
     * @return array
     */
    public function getPreviewData(MstRegulation $regulation): array
    {
        $regulation->load(['parent']);

        if (strtolower($regulation->tipe ?? '') === 'procedure') {
            $actors = MstActor::with('organization')
                ->where('regulation_id', $regulation->id)
                ->get();

            $categories = TrsSopCategory::where('regulation_id', $regulation->id)
                ->orderBy('id')
                ->get();

            $sop = MstSop::with(['category', 'regulation'])
                ->whereHas('category', function ($q) use ($regulation) {
                    $q->where('regulation_id', $regulation->id);
                })
                ->orderBy('category_id')
                ->orderBy('id')
                ->get();

            $flowChartSops = MstSop::with(['category', 'mapActorSops.actor.organization'])
                ->whereHas('category', function ($q) use ($regulation) {
                    $q->where('regulation_id', $regulation->id);
                })
                ->orderBy('category_id')
                ->orderBy('id')
                ->get();

            $tkoSections = TrsTkoSections::with(['contents' => function ($q) use ($regulation) {
                $q->where('regulation_id', $regulation->id);
            }])
            ->orderBy('order')
            ->get();

            return [
                'tipe' => 'Procedure',
                'regulation' => $regulation,
                'actors' => $actors,
                'categories' => $categories,
                'sop' => $sop,
                'flowChartSops' => $flowChartSops,
                'tkoSections' => $tkoSections,
            ];
        } else {
            $policies = $regulation->generalPolicies()->orderBy('number')->get();

            $objectives = MstObjective::with(['practices' => function($query) {
                $query->orderBy('practice_id', 'asc');
            }])
            ->where('regulation_id', $regulation->id)
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

            if ($objectives->isEmpty()) {
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
            }

            $roles = MstRole::with(['responsibilities' => function ($query) {
                $query->orderBy('id', 'asc');
            }])->orderBy('id', 'asc')->get();

            return [
                'tipe' => 'Policy',
                'regulation' => $regulation,
                'policies' => $policies,
                'objectives' => $objectives,
                'roles' => $roles,
            ];
        }
    }
}
