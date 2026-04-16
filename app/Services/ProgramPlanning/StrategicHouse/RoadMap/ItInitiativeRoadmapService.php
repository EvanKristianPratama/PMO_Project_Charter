<?php

namespace App\Services\ProgramPlanning\StrategicHouse\RoadMap;

use App\Models\Milestone;
use App\Models\MstInitiative;

class ItInitiativeRoadmapService
{
    private const DEFAULT_START_YEAR = 2025;
    private const DEFAULT_END_YEAR   = 2029;

    public function getPageProps(): array
    {
        $initiatives = MstInitiative::query()
            ->where('tipe_initiative', 2)
            ->with([
                'coe:id,name',
                'mappedProjects' => fn ($q) => $q->with([
                    'charters' => fn ($cq) => $cq
                        ->select(['id', 'project_id', 'version_label', 'objectives', 'duration'])
                        ->with(['milestones' => fn ($mq) => $mq
                            ->select(['id', 'pc_id', 'version', 'title', 'output', 'start_date', 'end_date', 'type', 'milestone_type', 'order'])
                            ->orderBy('order')
                            ->orderBy('id'),
                        ])
                        ->orderByDesc('id'),
                ]),
            ])
            ->orderBy('code')
            ->orderBy('id')
            ->get();

        $groups       = [];
        $globalNumber = 1;

        $byCoE = $initiatives->groupBy(
            fn (MstInitiative $i) => $i->coe?->name ?? 'Uncategorized',
        );

        foreach ($byCoE as $coeName => $groupInitiatives) {
            $initiativeData = [];

            foreach ($groupInitiatives as $initiative) {
                // Flatten all charters from all mapped projects
                $projects = $initiative->mappedProjects
                    ->flatMap(fn ($project) => $project->charters->map(
                        fn ($charter) => $this->mapCharter($charter, $project->name, $project->code),
                    ))
                    ->values()
                    ->all();

                $initiativeData[] = [
                    'no'       => $globalNumber++,
                    'id'       => (int) $initiative->id,
                    'name'     => trim((string) ($initiative->name ?? '-')),
                    'projects' => $projects,
                ];
            }

            $groups[] = [
                'coe_name'    => $coeName,
                'initiatives' => $initiativeData,
            ];
        }

        return [
            'groups'               => $groups,
            'startYear'            => self::DEFAULT_START_YEAR,
            'endYear'              => self::DEFAULT_END_YEAR,
            'totalCount'           => $initiatives->count(),
            'milestoneTypeOptions' => Milestone::roadmapTypeOptions(),
        ];
    }

    private function mapCharter($charter, string $projectName, ?string $projectCode): array
    {
        $versionLabel = $this->normalizeVersionLabel($charter->version_label);

        $milestones = ($charter->milestones ?? collect())
            ->map(fn ($ms) => [
                'id'             => (int) $ms->id,
                'pc_id'          => (int) $ms->pc_id,
                'version'        => $versionLabel,
                'title'          => $ms->title ?? '-',
                'output'         => $ms->output ?? '',
                'start_date'     => $ms->start_date?->format('Y-m-d'),
                'end_date'       => $ms->end_date?->format('Y-m-d'),
                'type'           => $ms->type,
                'milestone_type' => $ms->milestone_type,
                'order'          => $ms->order,
            ])
            ->values()
            ->all();

        return [
            'id'            => (int) $charter->id,
            'project_id'    => (int) $charter->project_id,
            'name'          => $projectName,
            'code'          => $projectCode,
            'version_label' => $charter->version_label,
            'objectives'    => $charter->objectives,
            'duration'      => $charter->duration,
            'milestones'    => $milestones,
            'charter'       => [
                'id'            => (int) $charter->id,
                'project_id'    => (int) $charter->project_id,
                'version_label' => $charter->version_label,
                'objectives'    => $charter->objectives,
                'duration'      => $charter->duration,
            ],
        ];
    }

    private function normalizeVersionLabel(mixed $value): string
    {
        $raw = strtolower(trim((string) $value));

        if ($raw === '' || $raw === 'v') {
            return 'v1';
        }

        if (preg_match('/^v(\d+)$/', $raw, $matches) === 1) {
            return 'v' . max(1, (int) $matches[1]);
        }

        if (preg_match('/^\d+$/', $raw) === 1) {
            return 'v' . max(1, (int) $raw);
        }

        return $raw;
    }
}
