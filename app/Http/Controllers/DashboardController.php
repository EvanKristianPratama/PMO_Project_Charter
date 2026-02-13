<?php

namespace App\Http\Controllers;

use App\Models\DigitalInitiative;
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
        ]);
    }
}
