<?php

namespace App\Services\StrategicHouse\StrategicPillars;

use App\Models\Goal;
use App\Models\InitiativeTagging;
use App\Models\MstInitiative;
use App\Models\Theme;
use App\Models\TrsOrganization;
use Illuminate\Support\Collection;

class StrategicPillarPageService
{
    private const DEFAULT_PILAR = '1';

    private const PILAR_OPTIONS = [
        '1' => [
            'label' => 'Pilar 1',
            'name' => 'Dual Growth Strategy Compendium',
        ],
        '2' => [
            'label' => 'Pilar 2',
            'name' => 'Dual Growth Strategy RSTI 2025-2029',
        ],
    ];

    public function getPageProps(?string $goalId, ?string $organizationId, ?string $pilarId, int $initiativeType = 1, ?Collection $providedInitiatives = null): array
    {
        $selectedPilar = $this->normalizePilar($pilarId);
        $pillars = $this->getStrategicPillars($selectedPilar);
        
        $allInitiatives = $providedInitiatives ?? MstInitiative::query()
            ->select('id', 'code', 'name', 'description', 'business_unit', 'tipe_initiative', 'source')
            ->with([
                'organization:id,name', 
                'latestStatusImplementation:id,initiative_id,review_status', 
                'taggings:id,initiative_id,pilar,goal,themes_id'
            ])
            ->orderBy('code')
            ->get();

        return [
            'strategicPillars' => $pillars,
            'taggings' => fn () => $this->getTaggings($selectedPilar, $initiativeType),
            'filters' => $this->getFilters($goalId, $organizationId, $selectedPilar),
            'allGoals' => $pillars->map(fn (Goal $goal): array => [
                'id' => $goal->id,
                'code' => $goal->code,
                'title' => $goal->title,
                'pilar' => $goal->pilar,
            ]),
            'allOrganizations' => fn () => $this->getAllOrganizations(),
            'allInitiatives' => $allInitiatives->map(fn (MstInitiative $initiative): array => [
                'id' => (int) $initiative->id,
                'code' => $initiative->code,
                'name' => $initiative->name,
                'description' => $initiative->description,
                'tipe_initiative' => (int) $initiative->tipe_initiative,
                'source' => $initiative->source ? (int) $initiative->source : null,
                'implementation_status' => $initiative->latestStatusImplementation?->review_status,
                'organization' => $initiative->organization
                    ? ['id' => $initiative->organization->id, 'name' => $initiative->organization->name]
                    : null,
            ])->values(),
            'allThemes' => $pillars->flatMap(fn (Goal $goal) => collect($goal->themes)->map(fn ($theme): array => [
                'id' => $theme->id,
                'name' => $theme->name,
                'theme_number' => $theme->theme_number,
                'idGoal' => $theme->idGoal,
                'goal' => [
                    'id' => $goal->id,
                    'code' => $goal->code,
                    'title' => $goal->title,
                    'pilar' => $goal->pilar,
                ],
            ])),
            'matrixInitiatives' => $allInitiatives
                ->where('tipe_initiative', $initiativeType)
                ->filter(fn (MstInitiative $initiative): bool => $initiative->taggings->contains(function (InitiativeTagging $tagging) use ($selectedPilar): bool {
                    if ($selectedPilar === self::DEFAULT_PILAR) {
                        return (string) $tagging->pilar === self::DEFAULT_PILAR || is_null($tagging->pilar);
                    }
                    return (string) $tagging->pilar === $selectedPilar;
                }))
                ->map(fn (MstInitiative $initiative): array => [
                    'id' => (int) $initiative->id,
                    'code' => $initiative->code,
                    'name' => $initiative->name,
                    'description' => $initiative->description,
                    'source' => $initiative->source ? (int) $initiative->source : null,
                    'implementation_status' => $initiative->latestStatusImplementation?->review_status,
                ])
                ->values(),
            'pilarOptions' => $this->getPilarOptions(),
        ];
    }

    public function getFilters(?string $goalId, ?string $organizationId, string $pilarId): array
    {
        return [
            'goal_id' => filled($goalId) ? (int) $goalId : null,
            'org_id' => filled($organizationId) ? (int) $organizationId : null,
            'pilar' => (int) $pilarId,
        ];
    }

    public function getStrategicPillars(string $pilarId): Collection
    {
        $query = Goal::query()
            ->with(['themes' => fn ($query) => $query->orderBy('theme_number', 'asc')])
            ->orderBy('code', 'asc');

        $this->applyGoalPilarFilter($query, $pilarId);

        return $query->get();
    }

    public function getTaggings(string $pilarId, int $initiativeType = 1): Collection
    {
        $query = InitiativeTagging::query()
            ->with([
                'initiative:id,name,code,status,business_unit,tipe_initiative,source,description',
                'initiative.latestStatus',
                'initiative.organization:id,name',
                'initiative.mapSc:id,sc_id,initiative_id',
                'initiative.mappedProjects:id',
                'initiative.latestStatusImplementation',
                'theme:id,name,idGoal',
            ])
            ->whereHas('initiative', fn ($query) => $query->where('tipe_initiative', $initiativeType))
            ->orderByDesc('created_at');

        $this->applyTaggingPilarFilter($query, $pilarId);

        return $query->get();
    }

    public function getAllOrganizations(): Collection
    {
        return TrsOrganization::query()
            ->has('initiatives')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
    }

    public function getPilarOptions(): array
    {
        return collect(self::PILAR_OPTIONS)
            ->map(fn (array $option, string $id): array => [
                'id' => (int) $id,
                'label' => $option['label'],
                'name' => $option['name'],
            ])
            ->values()
            ->all();
    }

    private function normalizePilar(?string $pilarId): string
    {
        return array_key_exists((string) $pilarId, self::PILAR_OPTIONS)
            ? (string) $pilarId
            : self::DEFAULT_PILAR;
    }

    private function applyGoalPilarFilter($query, string $pilarId): void
    {
        if ($pilarId === self::DEFAULT_PILAR) {
            $query->where(function ($subQuery): void {
                $subQuery->where('pilar', self::DEFAULT_PILAR)
                    ->orWhereNull('pilar');
            });

            return;
        }

        $query->where('pilar', $pilarId);
    }

    private function applyTaggingPilarFilter($query, string $pilarId): void
    {
        if ($pilarId === self::DEFAULT_PILAR) {
            $query->where(function ($subQuery): void {
                $subQuery->where('pilar', self::DEFAULT_PILAR)
                    ->orWhereNull('pilar');
            });

            return;
        }

        $query->where('pilar', $pilarId);
    }
}
