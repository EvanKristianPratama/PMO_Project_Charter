<?php

namespace App\Http\Controllers\ProgramImplementation;

use App\Http\Controllers\Concerns\ResolvesInitiativeStatus;
use App\Http\Controllers\Controller;
use App\Models\DigitalInitiative;
use App\Models\MstInitiative;
use App\Models\TrsProject;
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
        $baselineStatusId      = $this->baselineStatusId($statusOptions);
        
        $digitalStatusCountsRaw = \App\Models\DigitalInitiative::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $itStatusCountsRaw     = TrsProject::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return Inertia::render('ProgramImplementation/Dashboard', [
            'overview' => [
                'status_options'        => $statusOptions,
                'digital_status_counts' => $this->mapCountsByStatus($statusOptions, $digitalStatusCountsRaw),
                'status_counts'      => $this->mapCountsByStatus($statusOptions, $itStatusCountsRaw),
            ],
            'completedStatusId'      => $baselineStatusId,
            'openDigitalInitiatives' => $this->openDigitalInitiatives($baselineStatusId),
            'openItInitiatives'      => $this->openItInitiatives($baselineStatusId),
        ]);
    }

    private function openDigitalInitiatives(int $baselineStatusId)
    {
        return DigitalInitiative::query()
            ->with(['statusRef:id,name', 'ucStatusImplementations'])
            ->latest()
            ->get();
    }

    private function openItInitiatives(int $baselineStatusId)
    {
        return TrsProject::query()
            ->with([
                'charter',
                'statusRef:id,name',
                'latestPcStatusImplementation',
                'pcStatusImplementations',
            ])
            ->latest()
            ->get();
    }
}
