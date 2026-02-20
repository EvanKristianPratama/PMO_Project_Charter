<?php

namespace App\Http\Controllers\ProgramImplementation;

use App\Http\Controllers\Concerns\ResolvesInitiativeStatus;
use App\Http\Controllers\Controller;
use App\Models\InitiativeStatus;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProgramImplementationController extends Controller
{
    use ResolvesInitiativeStatus;

    public function __invoke(): Response|RedirectResponse
    {
        if (request()->user()?->isAdminUser()) {
            return redirect()->route('admin.dashboard');
        }

        $statusOptions         = $this->statusOptions();
        $digitalStatusCountsRaw = collect();
        $itStatusCountsRaw     = Project::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return Inertia::render('ProgramImplementation/Index', [
            'projectCharterOverview' => [
                'status_options'        => $statusOptions,
                'digital_status_counts' => $this->mapCountsByStatus($statusOptions, $digitalStatusCountsRaw),
                'it_status_counts'      => $this->mapCountsByStatus($statusOptions, $itStatusCountsRaw),
            ],
        ]);
    }
}
