<?php

namespace App\Http\Controllers\ProgramEvaluation;

use App\Http\Controllers\Controller;
use App\Models\MstInitiative;
use App\Models\TrsPcStatusImplementation;
use App\Models\TrsMapPicProject;
use App\Models\TrsOrganization;
use Inertia\Response;

class ReviewAnalysisController extends Controller
{
    public function index(): Response
    {
        return inertia('ProgramEvaluation/ReviewAnalysis/Index', $this->buildAnalysisProps());
    }

    private function buildAnalysisProps(): array
    {
        $organizations = $this->buildOrganizationRows();
        $organizationLookup = $this->buildOrganizationLookup($organizations);
        $rows = $this->buildActorRows($organizations, $organizationLookup);

        $uniqueInitiatives = collect($rows)
            ->flatMap(static fn (array $row): array => array_values($row['initiative_map'] ?? []))
            ->keyBy('initiative_id')
            ->values();

        return [
            'rows' => collect($rows)
                ->sortBy([
                    ['organization_code_sort', 'asc'],
                    ['actor', 'asc'],
                ])
                ->values()
                ->map(function (array $row): array {
                    $row['it_building_blocks'] = array_values($row['it_building_block_map'] ?? []);
                    $row['initiatives'] = array_values($row['initiative_map'] ?? []);
                    $row['project_sponsors'] = array_values($row['project_sponsor_map'] ?? []);
                    $row['project_owners'] = array_values($row['project_owner_map'] ?? []);
                    $row['project_leaders'] = array_values($row['project_leader_map'] ?? []);
                    $row['cross_function_involvements'] = array_values($row['cross_function_map'] ?? []);
                    $row['personel_utama'] = array_values($row['personel_utama_map'] ?? []);
                    $row['total_count'] = count($row['initiative_map'] ?? []);

                    unset(
                        $row['it_building_block_map'],
                        $row['initiative_map'],
                        $row['project_sponsor_map'],
                        $row['project_owner_map'],
                        $row['project_leader_map'],
                        $row['cross_function_map'],
                        $row['personel_utama_map'],
                        $row['organization_code_sort'],
                    );

                    return $row;
                })
                ->all(),
            'summary' => [
                'totalOrganizations' => count($organizations),
                'totalInitiatives' => $uniqueInitiatives->count(),
                'totalSponsorAssignments' => collect($rows)->sum(static fn (array $row): int => count($row['project_sponsor_map'] ?? [])),
                'totalOwnerAssignments' => collect($rows)->sum(static fn (array $row): int => count($row['project_owner_map'] ?? [])),
                'totalLeaderAssignments' => collect($rows)->sum(static fn (array $row): int => count($row['project_leader_map'] ?? [])),
                'totalCrossFunctionAssignments' => collect($rows)->sum(static fn (array $row): int => count($row['cross_function_map'] ?? [])),
            ],
        ];
    }

    private function buildOrganizationRows(): array
    {
        $sponsorIds = TrsMapPicProject::query()->whereNotNull('project_sponsor')->where('project_sponsor', '>', 0)->pluck('project_sponsor')->unique()->toArray();
        $ownerIds = TrsMapPicProject::query()->whereNotNull('project_owner')->where('project_owner', '>', 0)->pluck('project_owner')->unique()->toArray();
        $leaderIds = TrsMapPicProject::query()->whereNotNull('project_leader')->where('project_leader', '>', 0)->pluck('project_leader')->unique()->toArray();
        $picOrgIds = array_values(array_unique(array_merge($sponsorIds, $ownerIds, $leaderIds)));

        return TrsOrganization::query()
            ->select(['id', 'groub_id', 'code', 'name', 'alias', 'jabatan', 'pejabat'])
            ->with(['picProjects:id,organization_id,name'])
            ->whereIn('id', $picOrgIds)
            ->get()
            ->map(function (TrsOrganization $organization): array {
                $code = trim((string) ($organization->code ?? ''));

                return [
                    'organization_id' => (int) $organization->id,
                    'organization_code' => $code,
                    'organization_code_sort' => $this->normalizeOrganizationCodeForSort($code),
                    'organization_name' => trim((string) ($organization->name ?? '')),
                    'organization_alias' => trim((string) ($organization->alias ?? '')),
                    'actor' => trim((string) ($organization->jabatan ?? '')) !== ''
                        ? trim((string) $organization->jabatan)
                        : trim((string) $organization->name),
                    'jabatan' => trim((string) ($organization->jabatan ?? '')),
                    'pejabat' => trim((string) ($organization->pejabat ?? '')),
                    'pic_projects' => $organization->picProjects
                        ->map(static fn ($pic): array => [
                            'id' => (int) $pic->id,
                            'name' => trim((string) ($pic->name ?? '')),
                        ])
                        ->filter(static fn (array $pic): bool => $pic['name'] !== '')
                        ->values()
                        ->all(),
                ];
            })
            ->sort(function (array $left, array $right): int {
                return $this->compareOrganizationCode($left['organization_code_sort'], $right['organization_code_sort']);
            })
            ->values()
            ->all();
    }

    private function buildOrganizationLookup(array $organizations): array
    {
        $lookup = [];

        foreach ($organizations as $organization) {
            $orgId = (int) $organization['organization_id'];
            $code = trim((string) ($organization['organization_code'] ?? ''));

            foreach ([
                $organization['organization_name'] ?? '',
                $organization['organization_alias'] ?? '',
                $organization['actor'] ?? '',
                $organization['jabatan'] ?? '',
                $organization['pejabat'] ?? '',
                $code,
            ] as $value) {
                $normalized = $this->normalizeLabel($value);

                if ($normalized !== '') {
                    $lookup[$normalized] = $orgId;
                }
            }

            foreach (($organization['pic_projects'] ?? []) as $picProject) {
                $normalized = $this->normalizeLabel($picProject['name'] ?? '');

                if ($normalized !== '') {
                    $lookup[$normalized] = $orgId;
                }
            }
        }

        return $lookup;
    }

    private function buildActorRows(array $organizations, array $organizationLookup): array
    {
        $rows = [];

        foreach ($organizations as $organization) {
            $organizationId = (int) $organization['organization_id'];

            $rows[$organizationId] = [
                ...$organization,
                'it_building_block_map' => [],
                'initiative_map' => [],
                'project_sponsor_map' => [],
                'project_owner_map' => [],
                'project_leader_map' => [],
                'cross_function_map' => [],
                'personel_utama_map' => [],
            ];
        }

        $initiatives = MstInitiative::query()
            ->select(['id', 'code', 'name', 'tipe_initiative', 'coe_id'])
            ->whereIn('tipe_initiative', [1, 2])
            ->whereHas('mappedProjects')
            ->with([
                'coe:id,name',
                'mappedProjects' => static fn ($query) => $query
                    ->with([
                        'mapPicProject',
                        'mapCrossFunctions.organization',
                        'latestPcStatusImplementation',
                        'pcStatusImplementations' => static fn ($statusQuery) => $statusQuery
                            ->select([
                                'trs_pc_status_implementation.id',
                                'trs_pc_status_implementation.project_id',
                                'trs_pc_status_implementation.status',
                                'trs_pc_status_implementation.year',
                                'trs_pc_status_implementation.created_at',
                                'trs_pc_status_implementation.updated_at',
                            ])
                            ->orderByDesc('year')
                            ->orderByDesc('id'),
                        'projectCharters' => static fn ($charterQuery) => $charterQuery
                            ->select([
                                'trs_project_charters.id',
                                'trs_project_charters.project_id',
                                'trs_project_charters.version_label',
                                'trs_project_charters.sponsor',
                                'trs_project_charters.owner',
                                'trs_project_charters.leader',
                                'trs_project_charters.status',
                                'trs_project_charters.category',
                                'trs_project_charters.key_personnel',
                                'trs_project_charters.key_items',
                                'trs_project_charters.tgl_dokumen',
                            ])
                            ->orderByDesc('id'),
                    ])
                    ->orderBy('trs_projects.code')
                    ->orderBy('trs_projects.id'),
            ])
            ->orderByRaw('CASE WHEN code IS NULL OR code = "" THEN 1 ELSE 0 END')
            ->orderBy('code')
            ->orderBy('name')
            ->get();

        foreach ($initiatives as $index => $initiative) {
            $projects = $initiative->mappedProjects ?? collect();
            $latestProject = $projects->sortByDesc('id')->first();
            $latestCharter = $projects
                ->flatMap(static fn ($project) => $project->projectCharters ?? collect())
                ->sortByDesc('id')
                ->first();

            $latestReviewStatus = $this->resolveLatestReviewStatus($latestProject?->latestPcStatusImplementation?->status ?? null);
            $buildingBlock = trim((string) ($initiative->coe?->name ?? ''));
            $initiativeItem = [
                'initiative_id' => (int) $initiative->id,
                'no' => $index + 1,
                'code' => trim((string) ($initiative->code ?? '')) !== '' ? trim((string) $initiative->code) : '-',
                'name' => trim((string) ($initiative->name ?? '')) !== '' ? trim((string) $initiative->name) : '-',
                'building_block' => $buildingBlock !== '' ? $buildingBlock : '-',
                'status' => $latestReviewStatus,
            ];

            $sponsorId = (int) ($latestProject?->mapPicProject?->project_sponsor ?? 0);
            $ownerId = (int) ($latestProject?->mapPicProject?->project_owner ?? 0);
            $leaderId = (int) ($latestProject?->mapPicProject?->project_leader ?? 0);
            $personelUtamaItems = $this->buildCrossFunctionDisplayItems($projects, 1);
            $crossFunctionItems = $this->buildCrossFunctionDisplayItems($projects, 2);
            $personelUtamaIds = collect($personelUtamaItems)
                ->pluck('organization_id')
                ->filter()
                ->map(static fn ($id): int => (int) $id)
                ->unique()
                ->values()
                ->all();
            $crossFunctionIds = collect($crossFunctionItems)
                ->pluck('organization_id')
                ->filter()
                ->map(static fn ($id): int => (int) $id)
                ->unique()
                ->values()
                ->all();

            if ($ownerId === 0 && $latestCharter?->owner) {
                $ownerId = $organizationLookup[$this->normalizeLabel($latestCharter->owner)] ?? 0;
            }

            if ($leaderId === 0 && $latestCharter?->leader) {
                $leaderId = $organizationLookup[$this->normalizeLabel($latestCharter->leader)] ?? 0;
            }

            $matchedOrganizationIds = array_values(array_unique(array_filter([
                $sponsorId > 0 ? $sponsorId : null,
                $ownerId > 0 ? $ownerId : null,
                $leaderId > 0 ? $leaderId : null,
                ...$personelUtamaIds,
                ...$crossFunctionIds,
            ])));

            foreach ($matchedOrganizationIds as $organizationId) {
                if (!isset($rows[$organizationId])) {
                    continue;
                }

                $this->pushUnique($rows[$organizationId]['initiative_map'], $initiativeItem, 'initiative_id');
                $this->pushUnique($rows[$organizationId]['it_building_block_map'], [
                    'building_block' => $initiativeItem['building_block'],
                    'initiative_id' => $initiativeItem['initiative_id'],
                    'no' => $initiativeItem['no'],
                    'name' => $initiativeItem['name'],
                    'status' => $initiativeItem['status'],
                ], 'building_block');

                if ($organizationId === $sponsorId) {
                    $this->pushUnique($rows[$organizationId]['project_sponsor_map'], $initiativeItem, 'initiative_id');
                }

                if ($organizationId === $ownerId) {
                    $this->pushUnique($rows[$organizationId]['project_owner_map'], $initiativeItem, 'initiative_id');
                }

                if ($organizationId === $leaderId) {
                    $this->pushUnique($rows[$organizationId]['project_leader_map'], $initiativeItem, 'initiative_id');
                }

                if (in_array($organizationId, $crossFunctionIds, true)) {
                    $this->pushUnique($rows[$organizationId]['cross_function_map'], $initiativeItem, 'initiative_id');
                }

                if (in_array($organizationId, $personelUtamaIds, true)) {
                    foreach ($personelUtamaItems as $personelUtamaItem) {
                        $this->pushUnique($rows[$organizationId]['personel_utama_map'], $personelUtamaItem, 'pc_id');
                    }
                }
            }
        }

        return $rows;
    }

    /**
     * Build display items for the cross-function columns from `trs_map_cross_function`.
     *
     * Status `1` is used for Personel Utama and status `2` for Cross Function Involvement.
     * The label shown in the matrix is the `pc_id` from `trs_map_cross_function`, with
     * the project name and charter version kept in the tooltip for context.
     */
    private function buildCrossFunctionDisplayItems(iterable $projects, int $status): array
    {
        $items = [];
        $uniqueKeys = [];

        foreach ($projects as $project) {
            $projectId = (int) ($project->id ?? 0);
            $projectName = trim((string) ($project->name ?? ''));
            $latestCharter = collect($project->projectCharters ?? [])->first();
            $latestStatusImplementation = $this->resolveLatestProjectStatusImplementation($project);
            $charterVersion = trim((string) ($latestCharter?->version_label ?? ''));
            $displayName = $projectName !== '' ? $projectName : sprintf('Project #%d', $projectId);
            $noteParts = array_values(array_filter([
                $projectName !== '' ? 'Project ' . $projectName : '',
                $charterVersion !== '' ? 'Charter ' . $charterVersion : '',
            ]));
            $note = $noteParts !== [] ? implode(' | ', $noteParts) : null;

            foreach (($project->mapCrossFunctions ?? collect()) as $mapCrossFunction) {
                if ((int) ($mapCrossFunction->status ?? 0) !== $status) {
                    continue;
                }

                $organizationId = (int) ($mapCrossFunction->organization_id ?? 0);

                if ($organizationId <= 0) {
                    continue;
                }

                $pcId = $projectId > 0 ? $projectId : null;
                $uniqueKey = sprintf('%d|%d|%d', $organizationId, $pcId ?? 0, $status);

                if (isset($uniqueKeys[$uniqueKey])) {
                    continue;
                }

                $uniqueKeys[$uniqueKey] = true;

                $items[] = [
                    'organization_id' => $organizationId,
                    'initiative_id' => $pcId,
                    'project_id' => $pcId,
                    'pc_id' => $pcId,
                    'personel_key' => sprintf('%d|%d|%d', $organizationId, $pcId ?? 0, $status),
                    'no' => $pcId ?? '-',
                    'name' => $displayName,
                    'note' => $note,
                    'code' => $pcId ?? '-',
                    'implementation_status' => trim((string) ($latestStatusImplementation?->status ?? '')),
                    'status' => (int) ($mapCrossFunction->status ?? 0),
                ];
            }
        }

        return $items;
    }

    private function resolveLatestProjectStatusImplementation(mixed $project): mixed
    {
        $statusImplementations = collect($project?->pcStatusImplementations ?? [])
            ->sortBy([
                ['year', 'desc'],
                ['id', 'desc'],
            ])
            ->values();

        if ($statusImplementations->isNotEmpty()) {
            return $statusImplementations->first();
        }

        $latest = $project?->latestPcStatusImplementation ?? null;

        if ($latest) {
            return $latest;
        }

        $projectId = (int) ($project?->id ?? 0);

        if ($projectId <= 0) {
            return null;
        }

        return TrsPcStatusImplementation::query()
            ->select(['id', 'project_id', 'status', 'year'])
            ->where('project_id', $projectId)
            ->orderByDesc('year')
            ->orderByDesc('id')
            ->first();
    }

    private function pushUnique(array &$target, array $item, string $uniqueKey): void
    {
        $value = trim((string) ($item[$uniqueKey] ?? ''));

        if ($value === '') {
            return;
        }

        foreach ($target as $existing) {
            if (trim((string) ($existing[$uniqueKey] ?? '')) === $value) {
                return;
            }
        }

        $target[] = $item;
    }

    private function normalizeLabel(?string $value): string
    {
        return mb_strtolower(trim((string) $value));
    }

    private function normalizeOrganizationCodeForSort(?string $code): string
    {
        $digits = preg_replace('/\D+/', '', trim((string) $code)) ?? '';

        if ($digits === '') {
            return '';
        }

        return str_pad($digits, 7, '0', STR_PAD_RIGHT);
    }

    private function compareOrganizationCode(string $leftCode, string $rightCode): int
    {
        if ($leftCode === '' && $rightCode === '') {
            return 0;
        }

        if ($leftCode === '') {
            return 1;
        }

        if ($rightCode === '') {
            return -1;
        }

        return strcmp($leftCode, $rightCode);
    }

    private function resolveLatestReviewStatus(mixed $status): string
    {
        $normalized = trim((string) $status);

        if ($normalized === '') {
            return 'Belum Ada Status';
        }

        return $normalized;
    }
}
