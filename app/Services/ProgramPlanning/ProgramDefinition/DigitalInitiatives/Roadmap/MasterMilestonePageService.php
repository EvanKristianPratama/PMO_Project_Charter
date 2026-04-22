<?php

namespace App\Services\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Roadmap;

use App\Models\MstInitiative;
use App\Models\TrsMasterMilestone;
use App\Models\TrsOrganization;
use Illuminate\Support\Collection;

class MasterMilestonePageService
{
    public function getIndexPageProps(): array
    {
        return $this->sharedProps();
    }

    public function getCreatePageProps(?int $selectedInitiativeId = null): array
    {
        $props = $this->sharedProps();
        $availableInitiativeIds = collect($props['initiativeOptions'])
            ->pluck('id')
            ->filter(fn (mixed $id): bool => (int) $id > 0)
            ->map(fn (mixed $id): int => (int) $id);

        $props['selectedInitiativeId'] = $availableInitiativeIds->contains((int) $selectedInitiativeId)
            ? (int) $selectedInitiativeId
            : null;

        return $props;
    }

    public function getEditPageProps(?int $selectedInitiativeId = null, ?int $selectedMilestoneId = null): array
    {
        $props = $this->sharedProps();
        $milestones = collect($props['milestones']);

        $milestoneInitiativeIds = $milestones
            ->pluck('initiative_id')
            ->filter(fn (mixed $id): bool => (int) $id > 0)
            ->map(fn (mixed $id): int => (int) $id)
            ->unique()
            ->values();

        $resolvedInitiativeId = $milestoneInitiativeIds->contains((int) $selectedInitiativeId)
            ? (int) $selectedInitiativeId
            : null;

        $selectedMilestone = $milestones->firstWhere('id', (int) $selectedMilestoneId);

        if ($selectedMilestone && $resolvedInitiativeId !== null && (int) $selectedMilestone['initiative_id'] !== $resolvedInitiativeId) {
            $selectedMilestone = null;
        }

        if (! $selectedMilestone && $resolvedInitiativeId !== null) {
            $selectedMilestone = $milestones
                ->first(fn (array $milestone): bool => (int) $milestone['initiative_id'] === $resolvedInitiativeId);
        }

        if (! $selectedMilestone && $milestones->isNotEmpty()) {
            $selectedMilestone = $milestones->first();
        }

        $props['selectedMilestoneId'] = $selectedMilestone ? (int) $selectedMilestone['id'] : null;
        $props['selectedInitiativeId'] = $selectedMilestone
            ? (int) $selectedMilestone['initiative_id']
            : ($resolvedInitiativeId ?? null);

        return $props;
    }

    private function sharedProps(): array
    {
        $milestones = TrsMasterMilestone::query()
            ->with([
                'initiative:id,code,name,business_unit,coe_id',
                'initiative.organization:id,name',
                'initiative.coe:id,name',
            ])
            ->orderBy('id')
            ->get()
            ->map(fn (TrsMasterMilestone $milestone): array => $this->mapMilestone($milestone))
            ->values();

        $initiativeOptions = MstInitiative::query()
            ->with('organization:id,name')
            ->get(['id', 'code', 'name', 'business_unit'])
            ->map(fn (MstInitiative $initiative): array => [
                'id' => (int) $initiative->id,
                'code' => trim((string) ($initiative->code ?? '')),
                'name' => (string) $initiative->name,
                'organization_name' => trim((string) ($initiative->organization?->name ?? '')) ?: '-',
            ])
            ->sort(function (array $left, array $right): int {
                $leftCode = trim((string) ($left['code'] ?? ''));
                $rightCode = trim((string) ($right['code'] ?? ''));

                if ($leftCode !== '' && $rightCode !== '') {
                    $codeComparison = strnatcasecmp($leftCode, $rightCode);

                    if ($codeComparison !== 0) {
                        return $codeComparison;
                    }
                } elseif ($leftCode !== '' || $rightCode !== '') {
                    return $leftCode === '' ? 1 : -1;
                }

                return strnatcasecmp((string) ($left['name'] ?? ''), (string) ($right['name'] ?? ''));
            })
            ->values();

        $organizationOptions = TrsOrganization::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (TrsOrganization $organization): array => [
                'id' => (int) $organization->id,
                'name' => trim((string) ($organization->name ?? '')),
            ])
            ->filter(fn (array $organization): bool => $organization['name'] !== '')
            ->values();

        [$startYearRange, $endYearRange] = $this->resolveYearRange($milestones);

        return [
            'milestones' => $milestones,
            'roadmapItems' => $milestones,
            'initiativeOptions' => $initiativeOptions,
            'organizationOptions' => $organizationOptions,
            'availableVersions' => $milestones
                ->pluck('version')
                ->filter(fn (mixed $version): bool => trim((string) $version) !== '')
                ->unique()
                ->sortBy(fn (string $version) => $this->versionSortKey($version))
                ->values(),
            'startYearRange' => $startYearRange,
            'endYearRange' => $endYearRange,
            'usingDummyData' => false,
        ];
    }

    private function mapMilestone(TrsMasterMilestone $milestone): array
    {
        $initiative = $milestone->initiative;
        $initiativeCode = trim((string) ($initiative?->code ?? ''));
        $initiativeName = trim((string) ($initiative?->name ?? ''));
        $organizationName = trim((string) ($initiative?->organization?->name ?? ''));
        $coeName = trim((string) ($initiative?->coe?->name ?? ''));

        return [
            'id' => (int) $milestone->id,
            'initiative_id' => (int) $milestone->initiative_id,
            'initiative_code' => $initiativeCode,
            'initiative_name' => $initiativeName !== '' ? $initiativeName : sprintf('Initiative #%d', $milestone->initiative_id),
            'coe_name' => $coeName !== '' ? $coeName : '-',
            'organization_name' => $organizationName !== '' ? $organizationName : '-',
            'activity' => $milestone->activity,
            'startYear' => (int) $milestone->startYear,
            'startQ' => $this->normalizeQuarterLabel($milestone->startQ),
            'endYear' => (int) $milestone->endYear,
            'endQ' => $this->normalizeQuarterLabel($milestone->endQ),
            'version' => trim((string) ($milestone->version ?? '')),
        ];
    }

    private function resolveYearRange(Collection $milestones): array
    {
        $startYear = (int) ($milestones->min('startYear') ?? 2024);
        $endYear = (int) ($milestones->max('endYear') ?? 2029);

        $startYear = min($startYear > 0 ? $startYear : 2024, 2024);
        $endYear = max($endYear >= $startYear ? $endYear : $startYear, 2029);

        return [$startYear, $endYear];
    }

    private function normalizeQuarterLabel(mixed $value): string
    {
        $raw = strtoupper(trim((string) $value));

        if (preg_match('/Q?([1-4])/', $raw, $matches) === 1) {
            return sprintf('Q%d', (int) $matches[1]);
        }

        return 'Q1';
    }

    private function versionSortKey(string $version): string
    {
        $number = 0;

        if (preg_match('/(\d+(?:\.\d+)?)/', $version, $matches) === 1) {
            $number = (float) $matches[1];
        }

        return sprintf('%012.4f|%s', $number, strtolower($version));
    }
}
