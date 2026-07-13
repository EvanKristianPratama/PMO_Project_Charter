<?php

namespace Modules\ITSP\Services\ProgramPlanning\StrategicPillars;

use Modules\ITSP\Models\Goal;
use Modules\ITSP\Models\InitiativeTagging;
use Modules\ITSP\Models\Theme;
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
        $pilar = $payload['pilar'] ?? null;

        if ($themesId) {
            $theme = Theme::query()
                ->with('goal:id,code,pilar')
                ->find($themesId);

            $goal = $theme?->goal?->code ?? $goal;
            $pilar = $theme?->goal?->pilar ?? $pilar;
        } elseif (filled($goal)) {
            $matchedGoal = Goal::query()
                ->select('id', 'code', 'pilar')
                ->where('code', $goal)
                ->when(
                    filled($pilar),
                    fn ($query) => $query->where('pilar', (string) $pilar)
                )
                ->first();

            $pilar = $matchedGoal?->pilar ?? $pilar;
        }

        return [
            'initiative_id' => (int) $payload['initiative_id'],
            'themes_id' => $themesId ?: null,
            'goal' => blank($goal) ? null : $goal,
            'pilar' => blank($pilar) ? null : (string) $pilar,
        ];
    }

    private function ensureInitiativeTaggingIsUnique(array $payload): void
    {
        $initiativeTaggingExists = InitiativeTagging::query()
            ->where('initiative_id', $payload['initiative_id'])
            ->where('themes_id', $payload['themes_id'])
            ->where('goal', $payload['goal'])
            ->where('pilar', $payload['pilar'])
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
