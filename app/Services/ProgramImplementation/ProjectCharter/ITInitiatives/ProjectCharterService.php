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

        $this->syncProjectSummaryFields($project, $payload);

        $project->charters()->create([
            ...Arr::except($payload, ['version_label', 'owner_name']),
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

        $this->syncProjectSummaryFields($project, $payload);

        $charter->update([
            ...Arr::except($payload, ['owner_name']),
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
}
