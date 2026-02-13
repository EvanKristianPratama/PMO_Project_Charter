<?php

namespace App\Http\Controllers;

use App\Models\DigitalInitiative;
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

        $totalProjects = Project::query()->count();
        $totalDigitalInitiatives = DigitalInitiative::query()->count();
        $statusCounts = Project::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $digitalStatusCounts = DigitalInitiative::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $openDigitalInitiatives = DigitalInitiative::query()
            ->select(['id', 'no', 'type', 'projectOwner', 'useCase', 'status', 'updated_at'])
            ->where(static function ($query): void {
                $query
                    ->whereNull('status')
                    ->orWhere('status', '!=', 'completed');
            })
            ->latest()
            ->get();

        $openItInitiatives = Project::query()
            ->select(['id', 'code', 'name', 'status', 'updated_at'])
            ->with(['charter:id,project_id,category'])
            ->where(static function ($query): void {
                $query
                    ->whereNull('status')
                    ->orWhere('status', '!=', 'completed');
            })
            ->latest()
            ->get();

        return Inertia::render('Dashboard', [
            'overview' => [
                'total_projects' => $totalProjects,
                'total_digital_initiatives' => $totalDigitalInitiatives,
                'status_counts' => [
                    'draft' => (int) ($statusCounts['draft'] ?? 0),
                    'on_hold' => (int) ($statusCounts['on_hold'] ?? 0),
                    'active' => (int) ($statusCounts['active'] ?? 0),
                    'completed' => (int) ($statusCounts['completed'] ?? 0),
                ],
                'digital_status_counts' => [
                    'draft' => (int) ($digitalStatusCounts['draft'] ?? 0),
                    'on_hold' => (int) ($digitalStatusCounts['on_hold'] ?? 0),
                    'active' => (int) ($digitalStatusCounts['active'] ?? 0),
                    'completed' => (int) ($digitalStatusCounts['completed'] ?? 0),
                ],
            ],
            'openDigitalInitiatives' => $openDigitalInitiatives,
            'openItInitiatives' => $openItInitiatives,
        ]);
    }
}
