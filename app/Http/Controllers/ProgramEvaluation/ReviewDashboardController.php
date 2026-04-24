<?php

namespace App\Http\Controllers\ProgramEvaluation;

use App\Http\Controllers\Controller;
use App\Models\InitiativeStatus;
use App\Models\MstInitiative;
use App\Models\ProjectStatusHistory;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Support\Collection;
use Inertia\Response;

class ReviewDashboardController extends Controller
{
    public function index(): Response
    {
        $rows = MstInitiative::query()
            ->select(['id', 'code', 'name', 'tipe_initiative', 'coe_id'])
            ->whereIn('tipe_initiative', [1, 2])
            ->whereHas('mappedProjects')
            ->with([
                'coe:id,name',
                'mappedProjects' => static fn ($query) => $query
                    ->select(['trs_projects.id', 'trs_projects.code', 'trs_projects.name'])
                    ->with([
                        'projectStatusHistories' => static fn ($historyQuery) => $historyQuery
                            ->select([
                                'trs_project_status_history.id',
                                'trs_project_status_history.project_charter_id',
                                'trs_project_status_history.status',
                                'trs_project_status_history.tanggal',
                                'trs_project_status_history.version',
                            ])
                            ->orderByDesc('trs_project_status_history.tanggal')
                            ->orderByDesc('trs_project_status_history.id'),
                    ])
                    ->orderBy('trs_projects.code')
                    ->orderBy('trs_projects.id'),
            ])
            ->orderByRaw('CASE WHEN code IS NULL OR code = "" THEN 1 ELSE 0 END')
            ->orderBy('code')
            ->orderBy('name')
            ->get()
            ->map(function (MstInitiative $initiative, int $index): array {
                $projects = $initiative->mappedProjects ?? collect();
                $baselineDate = $this->resolveStatusDate($projects, InitiativeStatus::BASELINE);
                $approveDate = $this->resolveStatusDate($projects, InitiativeStatus::APPROVE);
                $processMonthValue = $this->resolveProcessMonthValue($baselineDate, $approveDate);

                return [
                    'no' => $index + 1,
                    'initiative_id' => (int) $initiative->id,
                    'building_block_type' => trim((string) ($initiative->coe?->name ?? '')) !== '' ? $initiative->coe->name : '-',
                    'initiative_name' => trim((string) $initiative->name) !== '' ? $initiative->name : '-',
                    'baseline_date' => $this->formatDate($baselineDate),
                    'approve_date' => $this->formatDate($approveDate),
                    'process_month_value' => $processMonthValue,
                    'process_month' => $this->formatProcessMonth($processMonthValue),
                ];
            })
            ->values();

        return inertia('ProgramEvaluation/ReviewDashboard/Index', [
            'rows' => $rows,
            'summary' => [
                'total' => $rows->count(),
                'buildingBlock' => $rows->count(),
            ],
        ]);
    }

    private function resolveStatusDate(Collection $projects, int $statusId): ?Carbon
    {
        /** @var ProjectStatusHistory|null $latest */
        $latest = $projects
            ->flatMap(static fn ($project) => $project->projectStatusHistories ?? collect())
            ->filter(static fn (ProjectStatusHistory $history) => (int) $history->status === $statusId)
            ->sortByDesc(static function (ProjectStatusHistory $history): string {
                $rawDate = $history->tanggal;
                $date = '';

                if ($rawDate instanceof DateTimeInterface) {
                    $date = Carbon::instance($rawDate)->format('Y-m-d');
                } elseif ($rawDate) {
                    $date = Carbon::parse((string) $rawDate)->format('Y-m-d');
                }

                return sprintf('%s-%09d', $date, (int) $history->id);
            })
            ->first();

        if (! $latest || ! $latest->tanggal) {
            return null;
        }

        return Carbon::parse($latest->tanggal);
    }

    private function formatDate(?Carbon $date): ?string
    {
        return $date?->translatedFormat('d M Y');
    }

    private function resolveProcessMonthValue(?Carbon $baselineDate, ?Carbon $approveDate): ?int
    {
        if (! $baselineDate || ! $approveDate) {
            return null;
        }

        if ($approveDate->lt($baselineDate)) {
            return null;
        }

        return (int) $baselineDate->diffInMonths($approveDate);
    }

    private function formatProcessMonth(?int $months): ?string
    {
        if ($months === null) {
            return null;
        }

        return sprintf('%d bulan', $months);
    }
}