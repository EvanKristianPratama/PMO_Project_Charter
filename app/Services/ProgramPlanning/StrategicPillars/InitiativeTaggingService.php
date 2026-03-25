<?php

namespace App\Services\ProgramPlanning\StrategicPillars;

use App\Models\InitiativeTagging;
use App\Models\Theme;
use Illuminate\Validation\ValidationException;

class InitiativeTaggingService
{
    public function createInitiativeTagging(array $payload): InitiativeTagging
    {
        $normalizedPayload = $this->normalizedPayload($payload);

        $this->ensureInitiativeTaggingIsUnique($normalizedPayload);

        $initiativeTagging = InitiativeTagging::query()->create($normalizedPayload);

        return $this->loadInitiativeTaggingRelations($initiativeTagging);
    }

    public function deleteInitiativeTagging(InitiativeTagging $initiativeTagging): void
    {
        $initiativeTagging->delete();
    }

    private function normalizedPayload(array $payload): array
    {
        $themesId = $payload['themes_id'] ?? null;
        $goal = $payload['goal'] ?? null;

        if ($themesId) {
            $theme = Theme::query()
                ->with('goal:id,code')
                ->find($themesId);

            $goal = $theme?->goal?->code ?? $goal;
        }

        return [
            'initiative_id' => (int) $payload['initiative_id'],
            'themes_id' => $themesId ?: null,
            'goal' => blank($goal) ? null : $goal,
        ];
    }

    private function ensureInitiativeTaggingIsUnique(array $payload): void
    {
        $initiativeTaggingExists = InitiativeTagging::query()
            ->where('initiative_id', $payload['initiative_id'])
            ->where('themes_id', $payload['themes_id'])
            ->where('goal', $payload['goal'])
            ->exists();

        if ($initiativeTaggingExists) {
            throw ValidationException::withMessages([
                'initiative_id' => 'Mapping ini sudah ada.',
            ]);
        }
    }

    private function loadInitiativeTaggingRelations(InitiativeTagging $initiativeTagging): InitiativeTagging
    {
        $initiativeTagging->load([
            'initiative:id,name,code',
            'theme:id,name,idGoal',
        ]);

        return $initiativeTagging;
    }
}
