<?php

namespace App\Http\Controllers;

use App\Models\DigitalInitiative;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DashboardMonitoringController extends Controller
{
    public function __invoke(): Response|RedirectResponse
    {
        if (request()->user()?->isAdminUser()) {
            return redirect()->route('admin.dashboard');
        }

        $itStatusCounts = Project::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $digitalStatusCounts = DigitalInitiative::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $itTotals = [
            'draft' => (int) ($itStatusCounts['draft'] ?? 0),
            'on_hold' => (int) ($itStatusCounts['on_hold'] ?? 0),
            'active' => (int) ($itStatusCounts['active'] ?? 0),
            'completed' => (int) ($itStatusCounts['completed'] ?? 0),
        ];

        $digitalTotals = [
            'draft' => (int) ($digitalStatusCounts['draft'] ?? 0),
            'on_hold' => (int) ($digitalStatusCounts['on_hold'] ?? 0),
            'active' => (int) ($digitalStatusCounts['active'] ?? 0),
            'completed' => (int) ($digitalStatusCounts['completed'] ?? 0),
        ];

        return Inertia::render('DashboardMonitoring', [
            'summary' => [
                'total_it_initiatives' => Project::query()->count(),
                'total_digital_initiatives' => DigitalInitiative::query()->count(),
                'total_all_initiatives' => Project::query()->count() + DigitalInitiative::query()->count(),
                'it_status_counts' => $itTotals,
                'digital_status_counts' => $digitalTotals,
                'combined_status_counts' => [
                    'draft' => $itTotals['draft'] + $digitalTotals['draft'],
                    'on_hold' => $itTotals['on_hold'] + $digitalTotals['on_hold'],
                    'active' => $itTotals['active'] + $digitalTotals['active'],
                    'completed' => $itTotals['completed'] + $digitalTotals['completed'],
                ],
            ],
        ]);
    }
}
