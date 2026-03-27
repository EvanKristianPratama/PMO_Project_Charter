<?php

namespace App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Roadmap;

use App\Http\Controllers\Controller;
use App\Models\TrsMasterMilestone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
    public function __invoke(): Response|RedirectResponse
    {
        if (request()->user()?->isAdminUser()) {
            return redirect()->route('admin.dashboard');
        }

        [$roadmapItems, $usingDummyData] = $this->loadRoadmapDataset();

        $availableVersions = $roadmapItems
            ->pluck('version')
            ->filter()
            ->unique()
            ->sortBy(fn (string $version) => $this->versionSortKey($version))
            ->values();

        $selectedVersion = trim((string) request()->query('version', ''));
        if ($selectedVersion === '' || ! $availableVersions->contains($selectedVersion)) {
            $selectedVersion = $availableVersions->first() ?? 'v1.0';
        }

        return Inertia::render('ProgramPlanning/ProgramDefinition/DigitalInitiatives/Roadmap/Index', [
            'roadmapItems' => $roadmapItems->values(),
            'availableVersions' => $availableVersions,
            'selectedVersion' => $selectedVersion,
            'startYearRange' => 2024,
            'endYearRange' => 2029,
            'usingDummyData' => $usingDummyData,
        ]);
    }

    private function loadRoadmapDataset(): array
    {
        $roadmapItems = $this->loadRoadmapItemsFromDatabase();

        if ($roadmapItems->isNotEmpty()) {
            return [$roadmapItems, false];
        }

        return [$this->dummyRoadmapItems(), true];
    }

    private function loadRoadmapItemsFromDatabase(): Collection
    {
        if (! Schema::hasTable('trs_master_milestone')) {
            return collect();
        }

        $columns = collect(Schema::getColumnListing('trs_master_milestone'));

        $initiativeIdColumn = $this->firstExistingColumn($columns, [
            'initiative_id',
            'initiativeId',
            'mst_initiative_id',
            'use_case_id',
            'usecase_id',
        ]);
        $initiativeNameColumn = $this->firstExistingColumn($columns, [
            'initiative_name',
            'initiativeName',
            'initiative',
            'use_case_name',
            'usecase_name',
        ]);
        $activityColumn = $this->firstExistingColumn($columns, [
            'activity',
            'title',
            'name',
            'output',
        ]);
        $versionColumn = $this->firstExistingColumn($columns, [
            'version',
            'version_label',
        ]);
        $startYearColumn = $this->firstExistingColumn($columns, [
            'start_year',
            'startYear',
            'startyear',
            'year_start',
        ]);
        $startQuarterColumn = $this->firstExistingColumn($columns, [
            'start_q',
            'startQ',
            'start_quarter',
            'startquarter',
        ]);
        $endYearColumn = $this->firstExistingColumn($columns, [
            'end_year',
            'endYear',
            'endyear',
            'year_end',
        ]);
        $endQuarterColumn = $this->firstExistingColumn($columns, [
            'end_q',
            'endQ',
            'end_quarter',
            'endquarter',
        ]);
        $startDateColumn = $this->firstExistingColumn($columns, [
            'start_date',
            'startDate',
            'tanggal_mulai',
            'date_start',
        ]);
        $endDateColumn = $this->firstExistingColumn($columns, [
            'end_date',
            'endDate',
            'tanggal_selesai',
            'date_end',
        ]);

        $hasDirectQuarterFields = $startYearColumn && $startQuarterColumn && $endYearColumn && $endQuarterColumn;
        $hasDateFields = $startDateColumn && $endDateColumn;

        if (! $activityColumn || (! $initiativeIdColumn && ! $initiativeNameColumn) || (! $hasDirectQuarterFields && ! $hasDateFields)) {
            return collect();
        }

        $query = TrsMasterMilestone::query()->from('trs_master_milestone as milestones');
        $hasInitiativeTable = $initiativeIdColumn && Schema::hasTable('mst_initiative');
        $hasOrganizationTable = $hasInitiativeTable
            && Schema::hasTable('trs_organization')
            && Schema::hasColumn('mst_initiative', 'business_unit');

        if ($hasInitiativeTable) {
            $query->leftJoin('mst_initiative as initiative', "milestones.{$initiativeIdColumn}", '=', 'initiative.id');
        }

        if ($hasOrganizationTable) {
            $query->leftJoin('trs_organization as organization', 'initiative.business_unit', '=', 'organization.id');
        }

        $select = ['milestones.id'];

        if ($initiativeIdColumn) {
            $select[] = "milestones.{$initiativeIdColumn} as initiative_id";
        }

        if ($initiativeNameColumn) {
            $select[] = "milestones.{$initiativeNameColumn} as initiative_name_raw";
        }

        $select[] = "milestones.{$activityColumn} as activity";

        if ($versionColumn) {
            $select[] = "milestones.{$versionColumn} as version";
        }

        if ($startYearColumn) {
            $select[] = "milestones.{$startYearColumn} as start_year";
        }

        if ($startQuarterColumn) {
            $select[] = "milestones.{$startQuarterColumn} as start_quarter";
        }

        if ($endYearColumn) {
            $select[] = "milestones.{$endYearColumn} as end_year";
        }

        if ($endQuarterColumn) {
            $select[] = "milestones.{$endQuarterColumn} as end_quarter";
        }

        if ($startDateColumn) {
            $select[] = "milestones.{$startDateColumn} as start_date";
        }

        if ($endDateColumn) {
            $select[] = "milestones.{$endDateColumn} as end_date";
        }

        if ($hasInitiativeTable) {
            $select[] = 'initiative.name as initiative_name_joined';
        }

        if ($hasOrganizationTable) {
            $select[] = 'organization.name as organization_name';
        }

        return $query
            ->selectRaw(implode(', ', $select))
            ->orderBy($initiativeIdColumn ? "milestones.{$initiativeIdColumn}" : 'milestones.id')
            ->orderBy('milestones.id')
            ->get()
            ->map(fn (object $row) => $this->mapRoadmapRow($row))
            ->filter()
            ->values();
    }

    private function mapRoadmapRow(object $row): ?array
    {
        $start = $this->resolveYearQuarter(
            $row->start_year ?? null,
            $row->start_quarter ?? null,
            $row->start_date ?? null,
        );
        $end = $this->resolveYearQuarter(
            $row->end_year ?? null,
            $row->end_quarter ?? null,
            $row->end_date ?? null,
        );

        if (! $start || ! $end) {
            return null;
        }

        if ($end['sortable'] < $start['sortable']) {
            [$start, $end] = [$end, $start];
        }

        $initiativeId = isset($row->initiative_id) && $row->initiative_id !== null
            ? (int) $row->initiative_id
            : null;

        $initiativeName = trim((string) ($row->initiative_name_joined ?? $row->initiative_name_raw ?? ''));
        if ($initiativeName === '') {
            $initiativeName = $initiativeId ? sprintf('Initiative #%d', $initiativeId) : 'Unknown Initiative';
        }

        $activity = trim((string) ($row->activity ?? ''));
        if ($activity === '') {
            $activity = 'Untitled Activity';
        }

        $organizationName = trim((string) ($row->organization_name ?? ''));

        return [
            'id' => (int) ($row->id ?? 0),
            'initiative_id' => $initiativeId,
            'organization_name' => $organizationName !== '' ? $organizationName : '-',
            'initiative_name' => $initiativeName,
            'activity' => $activity,
            'startYear' => $start['year'],
            'startQ' => sprintf('Q%d', $start['quarter']),
            'endYear' => $end['year'],
            'endQ' => sprintf('Q%d', $end['quarter']),
            'version' => $this->normalizeVersionLabel($row->version ?? null),
        ];
    }

    private function resolveYearQuarter(mixed $yearValue, mixed $quarterValue, mixed $dateValue): ?array
    {
        $year = is_numeric($yearValue) ? (int) $yearValue : null;
        $quarter = $this->normalizeQuarter($quarterValue);

        if ($year && $quarter) {
            return [
                'year' => $year,
                'quarter' => $quarter,
                'sortable' => ($year * 10) + $quarter,
            ];
        }

        if (! filled($dateValue)) {
            return null;
        }

        $timestamp = strtotime((string) $dateValue);
        if ($timestamp === false) {
            return null;
        }

        $parsedYear = (int) gmdate('Y', $timestamp);
        $month = (int) gmdate('n', $timestamp);
        $parsedQuarter = (int) ceil($month / 3);

        return [
            'year' => $parsedYear,
            'quarter' => $parsedQuarter,
            'sortable' => ($parsedYear * 10) + $parsedQuarter,
        ];
    }

    private function normalizeQuarter(mixed $value): ?int
    {
        $raw = strtoupper(trim((string) $value));

        if ($raw === '') {
            return null;
        }

        if (preg_match('/Q?([1-4])/', $raw, $matches) === 1) {
            return (int) $matches[1];
        }

        return null;
    }

    private function normalizeVersionLabel(mixed $value): string
    {
        $raw = trim((string) $value);

        return $raw !== '' ? $raw : 'v1.0';
    }

    private function versionSortKey(string $version): string
    {
        $number = 0;

        if (preg_match('/(\d+(?:\.\d+)?)/', $version, $matches) === 1) {
            $number = (float) $matches[1];
        }

        return sprintf('%012.4f|%s', $number, strtolower($version));
    }

    private function firstExistingColumn(Collection $columns, array $candidates): ?string
    {
        return collect($candidates)->first(fn (string $candidate) => $columns->contains($candidate));
    }

    private function dummyRoadmapItems(): Collection
    {
        return collect([
            [
                'id' => 1,
                'initiative_id' => 1,
                'organization_name' => 'Upstream',
                'initiative_name' => 'Buspro-X',
                'activity' => 'Release 1',
                'startYear' => 2024,
                'startQ' => 'Q1',
                'endYear' => 2024,
                'endQ' => 'Q3',
                'version' => 'v1.0',
            ],
            [
                'id' => 2,
                'initiative_id' => 1,
                'organization_name' => 'Upstream',
                'initiative_name' => 'Buspro-X',
                'activity' => 'Release 2',
                'startYear' => 2024,
                'startQ' => 'Q4',
                'endYear' => 2025,
                'endQ' => 'Q2',
                'version' => 'v1.0',
            ],
            [
                'id' => 3,
                'initiative_id' => 2,
                'organization_name' => 'Drilling & Well',
                'initiative_name' => 'AI drilling',
                'activity' => 'Phase 1',
                'startYear' => 2024,
                'startQ' => 'Q2',
                'endYear' => 2024,
                'endQ' => 'Q4',
                'version' => 'v1.0',
            ],
            [
                'id' => 4,
                'initiative_id' => 13,
                'organization_name' => 'Production & Optimization',
                'initiative_name' => 'Digital twin',
                'activity' => 'Real Time Opt. Piloting',
                'startYear' => 2024,
                'startQ' => 'Q1',
                'endYear' => 2025,
                'endQ' => 'Q2',
                'version' => 'v1.0',
            ],
            [
                'id' => 5,
                'initiative_id' => 25,
                'organization_name' => 'Infrastructure',
                'initiative_name' => 'IML Control',
                'activity' => 'Physical infrastructure',
                'startYear' => 2024,
                'startQ' => 'Q1',
                'endYear' => 2026,
                'endQ' => 'Q1',
                'version' => 'v1.0',
            ],
        ]);
    }
}
