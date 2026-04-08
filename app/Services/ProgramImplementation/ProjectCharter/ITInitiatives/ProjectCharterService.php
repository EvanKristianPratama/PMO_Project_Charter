<?php

namespace App\Services\ProgramImplementation\ProjectCharter\ITInitiatives;

use App\Models\ProjectCharter;
use App\Models\TrsProject;
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

        $project->charters()->create([
            ...$charterPayload,
            'version_label' => $versionLabel,
        ]);

        return $versionLabel;
    }

    public function updateProjectCharter(TrsProject $project, ProjectCharter $charter, array $payload): string
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

        return $versionLabel;
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
