<?php

namespace App\Services\ProgramImplementation\ProjectCharter\ITInitiatives;

use App\Models\TrsPcVersionAnalysis;
use App\Models\TrsProject;
use Illuminate\Support\Arr;

class VersionAnalysisService
{
    public function storeVersionAnalysis(TrsProject $project, array $payload): TrsPcVersionAnalysis
    {
        $identity = $this->identityPayload($payload);
        $analysis = TrsPcVersionAnalysis::query()->firstOrNew([
            'project_id' => $project->id,
            ...$identity,
        ]);

        $analysis->fill([
            ...$identity,
            ...$this->analysisPayload($payload),
        ]);
        $analysis->project_id = $project->id;
        $analysis->save();

        return $analysis->fresh();
    }

    public function updateVersionAnalysis(
        TrsProject $project,
        TrsPcVersionAnalysis $analysis,
        array $payload
    ): TrsPcVersionAnalysis {
        abort_if((int) $analysis->project_id !== (int) $project->id, 403, 'Version analysis does not belong to this project.');

        $analysis->update([
            ...$this->identityPayload($payload),
            ...$this->analysisPayload($payload),
        ]);

        return $analysis->fresh();
    }

    private function identityPayload(array $payload): array
    {
        return [
            'version_label' => $this->nullableTrimmedString($payload['version_label'] ?? null),
        ];
    }

    private function analysisPayload(array $payload): array
    {
        return Arr::only($payload, [
            'sponsor',
            'owner',
            'leader',
            'category',
            'duration',
            'tgl_dokumen',
            'target_kpi',
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
    }

    private function nullableTrimmedString(mixed $value): ?string
    {
        $normalized = trim((string) ($value ?? ''));
        return $normalized !== '' ? $normalized : null;
    }
}
