<?php

namespace App\Http\Controllers\MasterData\ProjectCharter;

use App\Http\Controllers\Controller;
use App\Models\InitiativeStatus;
use App\Models\MstInitiative;
use App\Models\PcStatusImplementation;
use App\Models\TrsProject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ProjectCharterController extends Controller
{
    /* ── Validation rules (DRY) ────────────────────── */

    private function projectRules(): array
    {
        return [
            'code' => 'nullable|string|max:100',
            'name' => 'required|string|max:255',
            'owner' => 'nullable|string|max:255',
            'owner_name' => 'nullable|string|max:255',
            'status' => ['required', 'integer', Rule::exists('trs_status_initiative', 'id')],
        ];
    }

    private function statusRules(): array
    {
        return [
            'pc_status' => 'nullable|string|max:255',
            'pc_review_status' => 'nullable|string|in:At Risk,On Track,Not Started,Not Signed',
        ];
    }

    /* ── Shared dropdown options (DRY) ─────────────── */

    private function dropdownOptions(): array
    {
        return [
            'statusOptions' => InitiativeStatus::ordered()
                ->map(fn (InitiativeStatus $s) => [
                    'id' => (int) $s->id,
                    'name' => $s->name,
                    'label' => ucfirst($s->name),
                ])
                ->values(),

            'masterItInitiatives' => MstInitiative::query()
                ->select(['id', 'code', 'name', 'description'])
                ->where('tipe_initiative', 2)
                ->orderBy('code')
                ->get()
                ->values(),
        ];
    }

    /* ── CRUD ──────────────────────────────────────── */

    public function index(): Response
    {
        $projects = TrsProject::with([
            'owner:id,name',
            'charter',
            'statusRef:id,name',
            'latestPcStatusImplementation',
        ])->orderBy('id', 'asc')->get();

        return Inertia::render('MasterData/ProjectCharter/Index', [
            'projects' => $projects,
            ...$this->dropdownOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->projectRules());
        $owner = trim((string) ($validated['owner'] ?? ''));
        if ($owner === '') {
            $owner = trim((string) ($validated['owner_name'] ?? ''));
        }
        unset($validated['owner'], $validated['owner_name']);

        $project = TrsProject::create($validated);
        if ($owner !== '') {
            $project->charters()->create([
                'owner' => $owner,
                'version_label' => 'v1',
            ]);
        }

        $statusData = $request->validate($this->statusRules());

        // Auto-create initial PcStatusImplementation
        $statusModel = InitiativeStatus::find($project->status);
        PcStatusImplementation::create([
            'project_id' => $project->id,
            'review_status' => $statusData['pc_review_status'] ?? 'Not Started',
            'status' => $statusData['pc_status'] ?? ($statusModel?->name ?? ''),
            'date' => now()->toDateString(),
            'time_start' => now()->toTimeString(),
        ]);

        return redirect()
            ->route('master-data.project-charter.index')
            ->with('success', 'Project Charter berhasil ditambahkan.');
    }

    public function update(Request $request, TrsProject $projectCharter): RedirectResponse
    {
        $validated = $request->validate($this->projectRules());
        $owner = trim((string) ($validated['owner'] ?? ''));
        if ($owner === '') {
            $owner = trim((string) ($validated['owner_name'] ?? ''));
        }
        unset($validated['owner'], $validated['owner_name']);

        $oldStatus = $projectCharter->status;

        $projectCharter->update($validated);
        if ($owner !== '') {
            $latestCharter = $projectCharter->charters()->latest('id')->first();
            if ($latestCharter) {
                $latestCharter->update(['owner' => $owner]);
            } else {
                $projectCharter->charters()->create([
                    'owner' => $owner,
                ]);
            }
        }

        $statusData = $request->validate($this->statusRules());

        // Log new PcStatusImplementation when status changes
        if ((string) $projectCharter->status !== (string) $oldStatus || ($statusData['pc_review_status'] ?? null)) {
            $statusModel = InitiativeStatus::find($projectCharter->status);
            PcStatusImplementation::updateOrCreate(
                [
                    'project_id' => $projectCharter->id,
                    'date' => now()->toDateString(),
                ],
                [
                    'review_status' => $statusData['pc_review_status'] ?? 'Not Started',
                    'status' => $statusData['pc_status'] ?? ($statusModel?->name ?? ''),
                    'time_start' => now()->toTimeString(),
                ]
            );
        }

        return redirect()
            ->route('master-data.project-charter.index')
            ->with('success', 'Project Charter berhasil diperbarui.');
    }

    public function destroy(TrsProject $projectCharter): RedirectResponse
    {
        $projectCharter->pcStatusImplementations()->delete();
        $projectCharter->delete();

        return redirect()
            ->route('master-data.project-charter.index')
            ->with('success', 'Project Charter berhasil dihapus.');
    }
}
