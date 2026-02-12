<?php

namespace App\Http\Controllers;

use App\Models\Milestone;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response|RedirectResponse
    {
        if (request()->user()?->isAdminUser()) {
            return redirect()->route('admin.dashboard');
        }

        $roadmapScope = static function ($query): void {
            $query->where(function ($builder): void {
                $builder
                    ->whereHas('charter', static function ($charter): void {
                        $charter
                            ->whereNotNull('duration')
                            ->where('duration', '!=', '');
                    })
                    ->orWhereHas('milestones', static function ($milestones): void {
                        $milestones
                            ->whereNotNull('start_date')
                            ->orWhereNotNull('end_date');
                    });
            });
        };

        $totalProjects = Project::query()->count();
        $roadmapProjects = Project::query()->where($roadmapScope)->count();
        $statusCounts = Project::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $quarterStart = now()->startOfQuarter()->toDateString();
        $quarterEnd = now()->endOfQuarter()->toDateString();
        $today = now()->toDateString();

        $milestonesThisQuarter = Milestone::query()
            ->where(function ($query) use ($quarterStart, $quarterEnd): void {
                $query
                    ->whereBetween('start_date', [$quarterStart, $quarterEnd])
                    ->orWhereBetween('end_date', [$quarterStart, $quarterEnd]);
            })
            ->count();

        $nextMilestones = Milestone::query()
            ->with('project:id,code,name')
            ->where(function ($query) use ($today): void {
                $query
                    ->whereDate('start_date', '>=', $today)
                    ->orWhereDate('end_date', '>=', $today);
            })
            ->orderByRaw('COALESCE(start_date, end_date) asc')
            ->limit(5)
            ->get()
            ->map(static function (Milestone $milestone): array {
                return [
                    'id' => $milestone->id,
                    'title' => $milestone->title,
                    'project_name' => $milestone->project?->name,
                    'project_code' => $milestone->project?->code,
                    'start_date' => $milestone->start_date?->toDateString(),
                    'end_date' => $milestone->end_date?->toDateString(),
                ];
            })
            ->values();

        return Inertia::render('Dashboard', [
            'overview' => [
                'total_projects' => $totalProjects,
                'roadmap_projects' => $roadmapProjects,
                'milestones_this_quarter' => $milestonesThisQuarter,
                'completed_projects' => (int) ($statusCounts['completed'] ?? 0),
                'status_counts' => [
                    'draft' => (int) ($statusCounts['draft'] ?? 0),
                    'on_hold' => (int) ($statusCounts['on_hold'] ?? 0),
                    'active' => (int) ($statusCounts['active'] ?? 0),
                    'completed' => (int) ($statusCounts['completed'] ?? 0),
                ],
            ],
            'nextMilestones' => $nextMilestones,
        ]);
    }
}
