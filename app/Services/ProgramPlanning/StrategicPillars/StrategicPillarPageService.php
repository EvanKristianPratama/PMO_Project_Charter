<?php

namespace App\Services\ProgramPlanning\StrategicPillars;

use App\Models\Goal;
use App\Models\InitiativeTagging;
use App\Models\MstInitiative;
use App\Models\Theme;
use App\Models\TrsOrganization;
use Illuminate\Support\Collection;

class StrategicPillarPageService
{
    public function getPageProps(?string $goalId, ?string $organizationId): array
    {
        return [
            'strategicPillars' => fn () => $this->getStrategicPillars(),
            'taggings' => fn () => $this->getTaggings(),
            'filters' => $this->getFilters($goalId, $organizationId),
            'allGoals' => fn () => $this->getAllGoals(),
            'allOrganizations' => fn () => $this->getAllOrganizations(),
            'allInitiatives' => fn () => $this->getAllInitiatives(),
            'allThemes' => fn () => $this->getAllThemes(),
            'matrixInitiatives' => fn () => $this->getMatrixInitiatives(),
        ];
    }

    public function getFilters(?string $goalId, ?string $organizationId): array
    {
        return [
            'goal_id' => filled($goalId) ? (int) $goalId : null,
            'org_id' => filled($organizationId) ? (int) $organizationId : null,
        ];
    }

    public function getStrategicPillars(): Collection
    {
        return Goal::query()
            ->with(['themes' => fn ($query) => $query->orderBy('theme_number', 'asc')])
            ->orderBy('code', 'asc')
            ->get();
    }

    public function getTaggings(): Collection
    {
        return InitiativeTagging::query()
            ->with([
                'initiative:id,name,code,status,business_unit,tipe_initiative',
                'initiative.latestStatus',
                'initiative.organization:id,name',
                'initiative.mapSc:id,sc_id,initiative_id',
                'initiative.mappedProjects:id',
                'theme:id,name,idGoal',
            ])
            ->whereHas('initiative', fn ($query) => $query->where('tipe_initiative', 1))
            ->orderByDesc('created_at')
            ->get();
    }

    public function getAllGoals(): Collection
    {
        return Goal::query()
            ->select('id', 'code', 'title')
            ->orderBy('code')
            ->get();
    }

    public function getAllOrganizations(): Collection
    {
        return TrsOrganization::query()
            ->has('initiatives')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
    }

    public function getAllInitiatives(): Collection
    {
        return MstInitiative::query()
            ->select('id', 'code', 'name', 'business_unit', 'tipe_initiative')
            ->with('organization:id,name')
            ->orderBy('code')
            ->get()
            ->map(fn (MstInitiative $initiative): array => [
                'id' => $initiative->id,
                'code' => $initiative->code,
                'name' => $initiative->name,
                'tipe_initiative' => $initiative->tipe_initiative,
                'organization' => $initiative->organization
                    ? ['id' => $initiative->organization->id, 'name' => $initiative->organization->name]
                    : null,
            ])
            ->values();
    }

    public function getAllThemes(): Collection
    {
        return Theme::query()
            ->with('goal:id,code,title')
            ->select('id', 'name', 'theme_number', 'idGoal')
            ->orderBy('theme_number')
            ->get();
    }

    public function getMatrixInitiatives(): Collection
    {
        return MstInitiative::query()
            ->select('id', 'code', 'name')
            ->where('tipe_initiative', 1)
            ->orderBy('code')
            ->get()
            ->map(fn (MstInitiative $initiative): array => [
                'id' => $initiative->id,
                'code' => $initiative->code,
                'name' => $initiative->name,
            ])
            ->values();
    }
}
