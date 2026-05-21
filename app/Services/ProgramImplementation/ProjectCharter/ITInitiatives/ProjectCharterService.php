<?php

namespace App\Services\ProgramImplementation\ProjectCharter\ITInitiatives;

use App\Models\TrsProject;
use App\Models\TrsProjectCharter;
use Illuminate\Support\Arr;

class ProjectCharterService
{
    public function storeProjectCharter(TrsProject $project, array $payload): string
    {
        $versionLabel = $this->resolveVersionLabel(
            trim((string) ($payload['version_label'] ?? '')),
            sprintf('v%d', $project->charters()->count() + 1)
        );
        $charterPayload = $this->charterPayload($payload);

        $this->syncProjectSummaryFields($project, $payload);

        $charter = $project->charters()->create([
            ...$charterPayload,
            'version_label' => $versionLabel,
        ]);

        $this->syncPicMappings($charter, $payload);

        return $versionLabel;
    }

    public function updateProjectCharter(TrsProject $project, TrsProjectCharter $charter, array $payload): string
    {
        abort_if((int) $charter->project_id !== (int) $project->id, 403, 'Charter does not belong to this project.');

        $versionLabel = $this->resolveVersionLabel(
            trim((string) ($payload['version_label'] ?? '')),
            $charter->version_label ?? sprintf('v%d', $project->charters()->count())
        );
        $charterPayload = $this->charterPayload($payload);

        $this->syncProjectSummaryFields($project, $payload);

        $charter->update([
            ...$charterPayload,
            'version_label' => $versionLabel,
        ]);

        $this->syncPicMappings($charter, $payload);

        return $versionLabel;
    }

    private function syncPicMappings(TrsProjectCharter $charter, array $payload): void
    {
        if (isset($payload['pic_sponsor_id'])) {
            \App\Models\TrsMapProjectSponsor::updateOrCreate(
                ['pc_id' => $charter->id],
                ['pic_id' => $payload['pic_sponsor_id']]
            );
        }

        if (isset($payload['pic_owner_id'])) {
            \App\Models\TrsMapProjectOwner::updateOrCreate(
                ['pc_id' => $charter->id],
                ['pic_id' => $payload['pic_owner_id']]
            );
        }

        if (isset($payload['pic_leader_id'])) {
            \App\Models\TrsMapProjectLeader::updateOrCreate(
                ['pc_id' => $charter->id],
                ['pic_id' => $payload['pic_leader_id']]
            );
        }

        if (isset($payload['pic_cross_function_ids']) && is_array($payload['pic_cross_function_ids'])) {
            \App\Models\TrsMapCrossFunction::where('pc_id', $charter->id)->delete();
            
            $mappings = array_map(function($picId) use ($charter) {
                return [
                    'pc_id' => $charter->id,
                    'pic_id' => $picId,
                ];
            }, array_filter($payload['pic_cross_function_ids']));

            if (!empty($mappings)) {
                \App\Models\TrsMapCrossFunction::insert($mappings);
            }
        }
    }

    private function syncProjectSummaryFields(TrsProject $project, array $payload): void
    {
        $projectFields = Arr::only($payload, ['owner_name', 'status']);
        $projectFields = array_filter($projectFields, static fn ($value) => $value !== null && $value !== '');

        if ($projectFields !== []) {
            $project->update($projectFields);
        }
    }

    private function resolveVersionLabel(string $versionLabel, string $fallbackVersionLabel): string
    {
        return $versionLabel !== '' ? $versionLabel : $fallbackVersionLabel;
    }

    private function charterPayload(array $payload): array
    {
        $charterPayload = Arr::only($payload, [
            'sponsor',
            'owner',
            'leader',
            'status',
            'tgl_dokumen',
            'category',
            'duration',
            'start_year',
            'end_year',
            'background',
            'objectives',
            'impact_value',
            'key_personnel',
            'key_items',
            'budget',
            'key_milestone',
            'risks_identified',
            'risk_mitigation',
            'notes',
        ]);

        $metadata = is_array($payload['metadata'] ?? null) ? $payload['metadata'] : [];
        $targetKpi = trim((string) ($payload['target_kpi'] ?? ''));

        unset($metadata['targetKpi'], $metadata['kpi_target'], $metadata['kpi']);

        if ($targetKpi !== '') {
            $metadata['target_kpi'] = $targetKpi;
        } else {
            unset($metadata['target_kpi']);
        }

        $charterPayload['metadata'] = $metadata !== [] ? $metadata : null;

        return $charterPayload;
    }
}
