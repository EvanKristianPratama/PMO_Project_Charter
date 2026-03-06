<?php

namespace App\Http\Controllers\ITInitiative;

use App\Http\Controllers\Controller;
use App\Http\Requests\ITInitiative\MilestoneStoreRequest;
use App\Models\Milestone;
use App\Models\ProjectCharter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MilestoneController extends Controller
{
    public function store(MilestoneStoreRequest $request, ProjectCharter $project): RedirectResponse
    {
        $charter = $project;
        $payload = $request->validated();
        $targetVersion = $this->normalizeVersionLabel($charter->version_label);
        $nextOrder = (int) (Milestone::query()
            ->where('pc_id', $charter->id)
            ->max('order') ?? 0) + 1;

        Milestone::query()->create([
            'pc_id' => (int) $charter->id,
            'version' => $targetVersion,
            'title' => $payload['title'],
            'output' => $payload['output'] ?? null,
            'type' => $payload['type'],
            'milestone_type' => Milestone::normalizeRoadmapType($payload['milestone_type'] ?? null),
            'start_date' => $payload['start_date'] ?? null,
            'end_date' => $payload['end_date'] ?? null,
            'order' => $nextOrder,
        ]);

        return back()->with('success', 'Roadmap activity added.');
    }

    public function update(MilestoneStoreRequest $request, ProjectCharter $project, Milestone $milestone): RedirectResponse
    {
        $charter = $project;

        if ((int) ($milestone->pc_id ?? 0) !== (int) $charter->id) {
            abort(404);
        }

        $payload = $request->validated();
        $targetVersion = $this->normalizeVersionLabel($charter->version_label);

        $milestone->update([
            'pc_id' => (int) $charter->id,
            'version' => $targetVersion,
            'title' => $payload['title'],
            'output' => $payload['output'] ?? null,
            'type' => $payload['type'],
            'milestone_type' => Milestone::normalizeRoadmapType($payload['milestone_type'] ?? null),
            'start_date' => $payload['start_date'] ?? null,
            'end_date' => $payload['end_date'] ?? null,
        ]);

        return back()->with('success', 'Roadmap activity updated.');
    }

    public function destroy(ProjectCharter $project, Milestone $milestone): RedirectResponse
    {
        $charter = $project;

        if ((int) ($milestone->pc_id ?? 0) !== (int) $charter->id) {
            abort(404);
        }

        $milestone->delete();

        return back()->with('success', 'Roadmap activity removed.');
    }

    public function createVersion(Request $request, ProjectCharter $project): RedirectResponse
    {
        return back()->with('warning', 'Versi roadmap mengikuti versi Project Charter. Buat versi charter baru untuk roadmap baru.');
    }

    private function normalizeVersionLabel(mixed $value): string
    {
        $raw = strtolower(trim((string) $value));

        if ($raw === '' || $raw === 'v') {
            return 'v1';
        }

        if (preg_match('/^v(\d+)$/', $raw, $matches) === 1) {
            return 'v'.max(1, (int) $matches[1]);
        }

        if (preg_match('/^\d+$/', $raw) === 1) {
            return 'v'.max(1, (int) $raw);
        }

        return $raw;
    }

}
