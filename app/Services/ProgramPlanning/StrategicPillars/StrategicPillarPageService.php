<?php

namespace App\Services\ProgramPlanning\StrategicPillars;

use Modules\ITSP\Models\Goal;
use Modules\ITSP\Models\InitiativeTagging;
use App\Models\MstInitiative;
use Modules\ITSP\Models\Theme;
use App\Models\TrsOrganization;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

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

    public function getPageProps(?string $goalId, ?string $organizationId, ?string $pilarId, int $initiativeType = 1): array
    {
        $selectedPilar = $this->normalizePilar($pilarId);

        return [
            'strategicPillars' => fn () => $this->getStrategicPillars($selectedPilar),
            'taggings' => fn () => $this->getTaggings($selectedPilar, $initiativeType),
            'filters' => $this->getFilters($goalId, $organizationId, $selectedPilar),
            'allGoals' => fn () => $this->getAllGoals($selectedPilar),
            'allOrganizations' => fn () => $this->getAllOrganizations(),
            'allInitiatives' => fn () => $this->getAllInitiatives(),
            'allThemes' => fn () => $this->getAllThemes($selectedPilar),
            'matrixInitiatives' => fn () => $this->getMatrixInitiatives($selectedPilar, $initiativeType),
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
        $cacheKey = "strategic_pillar_taggings_{$pilarId}_{$initiativeType}";
        return Cache::remember($cacheKey, 3600, function () use ($pilarId, $initiativeType) {
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
        });
    }

    public function getAllGoals(string $pilarId): Collection
    {
        $cacheKey = "strategic_pillar_all_goals_{$pilarId}";
        return Cache::remember($cacheKey, 3600, function () use ($pilarId) {
            $query = Goal::query()
                ->select('id', 'code', 'title', 'pilar')
                ->orderBy('code');

            $this->applyGoalPilarFilter($query, $pilarId);

            return $query->get();
        });
    }

    public function getAllOrganizations(): Collection
    {
        return Cache::remember('strategic_pillar_all_organizations', 3600, function () {
            return TrsOrganization::query()
                ->has('initiatives')
                ->select('id', 'name')
                ->orderBy('name')
                ->get();
        });
    }

    public function getAllInitiatives(): Collection
    {
        return Cache::remember('strategic_pillar_all_initiatives', 3600, function () {
            return MstInitiative::query()
                ->select('id', 'code', 'name', 'description', 'business_unit', 'tipe_initiative', 'source')
                ->with(['organization:id,name', 'latestStatusImplementation'])
                ->orderBy('code')
                ->get()
                ->map(fn (MstInitiative $initiative): array => [
                    'id' => $initiative->id,
                    'code' => $initiative->code,
                    'name' => $initiative->name,
                    'description' => $initiative->description,
                    'tipe_initiative' => $initiative->tipe_initiative,
                    'source' => $initiative->source ? (int) $initiative->source : null,
                    'implementation_status' => $initiative->latestStatusImplementation?->review_status,
                    'organization' => $initiative->organization
                        ? ['id' => $initiative->organization->id, 'name' => $initiative->organization->name]
                        : null,
                ])
                ->values();
        });
    }

    public function getAllThemes(string $pilarId): Collection
    {
        $cacheKey = "strategic_pillar_all_themes_{$pilarId}";
        return Cache::remember($cacheKey, 3600, function () use ($pilarId) {
            return Theme::query()
                ->with('goal:id,code,title,pilar')
                ->select('id', 'name', 'theme_number', 'idGoal')
                ->whereHas('goal', function ($query) use ($pilarId): void {
                    $this->applyGoalPilarFilter($query, $pilarId);
                })
                ->orderBy('idGoal')
                ->orderBy('theme_number')
                ->get();
        });
    }

    public function getMatrixInitiatives(string $pilarId, int $initiativeType = 1): Collection
    {
        $cacheKey = "strategic_pillar_matrix_initiatives_{$pilarId}_{$initiativeType}";
        return Cache::remember($cacheKey, 3600, function () use ($pilarId, $initiativeType) {
            return MstInitiative::query()
                ->select('id', 'code', 'name', 'description', 'source')
                ->with('latestStatusImplementation')
                ->where('tipe_initiative', $initiativeType)
                ->whereHas('taggings', function ($query) use ($pilarId): void {
                    $this->applyTaggingPilarFilter($query, $pilarId);
                })
                ->orderBy('code')
                ->get()
                ->map(fn (MstInitiative $initiative): array => [
                    'id' => $initiative->id,
                    'code' => $initiative->code,
                    'name' => $initiative->name,
                    'description' => $initiative->description,
                    'source' => $initiative->source ? (int) $initiative->source : null,
                    'implementation_status' => $initiative->latestStatusImplementation?->review_status,
                ])
                ->values();
        });
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
