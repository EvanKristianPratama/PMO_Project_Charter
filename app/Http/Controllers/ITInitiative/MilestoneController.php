<?php

namespace App\Http\Controllers\ITInitiative;

use App\Http\Controllers\Controller;
use App\Http\Requests\ITInitiative\MilestoneStoreRequest;
use App\Models\Milestone;
use App\Models\TrsProject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class MilestoneController extends Controller
{
    public function store(MilestoneStoreRequest $request, TrsProject $project): RedirectResponse
    {
        $payload = $request->validated();
        $targetVersion = $this->resolveVersionForProject($project, $payload['version'] ?? null);
        $nextOrder = (int) ($this->milestonesForVersion($project, $targetVersion)
            ->max('order') ?? 0) + 1;

        $project->milestones()->create([
            'version' => $targetVersion,
            'title' => $payload['title'],
            'output' => $payload['output'] ?? null,
            'type' => $payload['type'],
            'milestone_type' => Milestone::normalizeRoadmapType($payload['milestone_type'] ?? null),
            'start_date' => $payload['start_date'] ?? null,
            'end_date' => $payload['end_date'] ?? null,
            'order' => $nextOrder,
        ]);

        $this->persistRoadmapVersionMeta($project, $this->projectVersionLabels($project, false));

        return back()->with('success', 'Roadmap activity added.');
    }

    public function update(MilestoneStoreRequest $request, TrsProject $project, Milestone $milestone): RedirectResponse
    {
        if ((int) $milestone->project_id !== (int) $project->id) {
            abort(404);
        }

        $payload = $request->validated();
        $targetVersion = $this->resolveVersionForProject($project, $payload['version'] ?? $milestone->version);

        $milestone->update([
            'version' => $targetVersion,
            'title' => $payload['title'],
            'output' => $payload['output'] ?? null,
            'type' => $payload['type'],
            'milestone_type' => Milestone::normalizeRoadmapType($payload['milestone_type'] ?? null),
            'start_date' => $payload['start_date'] ?? null,
            'end_date' => $payload['end_date'] ?? null,
        ]);

        $this->persistRoadmapVersionMeta($project, $this->projectVersionLabels($project, false));

        return back()->with('success', 'Roadmap activity updated.');
    }

    public function destroy(TrsProject $project, Milestone $milestone): RedirectResponse
    {
        if ((int) $milestone->project_id !== (int) $project->id) {
            abort(404);
        }

        $milestone->delete();

        return back()->with('success', 'Roadmap activity removed.');
    }

    public function createVersion(Request $request, TrsProject $project): RedirectResponse
    {
        $payload = $request->validate([
            'redirect_to' => ['nullable', 'string', Rule::in(['add', 'edit'])],
        ]);

        $this->normalizeLegacyVersionDataForProject($project);

        $versions = $this->projectVersionLabels($project, false);
        $newVersion = $this->nextVersionLabel($versions);
        $this->persistRoadmapVersionMeta(
            $project,
            $versions->push($newVersion)->unique()->values(),
            $newVersion
        );

        $redirectTo = ($payload['redirect_to'] ?? 'edit') === 'add'
            ? 'roadmap.add'
            : 'roadmap.edit';

        return redirect()
            ->route($redirectTo, [
                'project_id' => $project->id,
                'version' => $newVersion,
            ])
            ->with('success', sprintf('Roadmap version %s berhasil dibuat.', $newVersion));
    }

    private function resolveVersionForProject(TrsProject $project, mixed $requestedVersion): string
    {
        $this->normalizeLegacyVersionDataForProject($project);
        $existingVersions = $this->projectVersionLabels($project, false);

        if ($requestedVersion !== null && trim((string) $requestedVersion) !== '') {
            $normalizedRequested = $this->normalizeVersionLabel($requestedVersion);

            if ($existingVersions->isEmpty() && $normalizedRequested === 'v1') {
                return 'v1';
            }

            if (!$existingVersions->contains($normalizedRequested)) {
                abort(422, 'Roadmap version tidak valid untuk project ini.');
            }

            return $normalizedRequested;
        }

        if ($existingVersions->isNotEmpty()) {
            return (string) $existingVersions->first();
        }

        return 'v1';
    }

    private function projectVersionLabels(TrsProject $project, bool $withDefault = true): Collection
    {
        $labelsFromMilestones = $project->milestones()
            ->reorder()
            ->select('version')
            ->distinct()
            ->pluck('version')
            ->values();

        $labels = $labelsFromMilestones
            ->merge($this->roadmapVersionLabelsFromMeta($project))
            ->map(fn ($label) => $this->normalizeVersionLabel($label))
            ->filter()
            ->unique()
            ->sortByDesc(fn (string $label): int => $this->extractVersionNumber($label))
            ->values();

        if ($withDefault && $labels->isEmpty()) {
            return collect(['v1']);
        }

        return $labels;
    }

    private function nextVersionLabel(Collection $existingLabels): string
    {
        if ($existingLabels->isEmpty()) {
            return 'v1';
        }

        $maxVersionNumber = $existingLabels
            ->map(fn (string $label): int => $this->extractVersionNumber($label))
            ->max();

        $maxVersionNumber = max((int) $maxVersionNumber, 1);

        return 'v'.($maxVersionNumber + 1);
    }

    private function normalizeLegacyVersionDataForProject(TrsProject $project): void
    {
        $project->milestones()
            ->where(function ($query): void {
                $query->whereNull('version')->orWhere('version', '');
            })
            ->update(['version' => 'v1']);
    }

    private function roadmapVersionLabelsFromMeta(TrsProject $project): Collection
    {
        $metadata = is_array($project->metadata) ? $project->metadata : [];
        $labels = $metadata['roadmap_versions'] ?? [];

        if (!is_array($labels)) {
            return collect();
        }

        return collect($labels);
    }

    private function persistRoadmapVersionMeta(TrsProject $project, Collection $labels, ?string $activeVersion = null): void
    {
        $normalizedLabels = $labels
            ->map(fn ($label) => $this->normalizeVersionLabel($label))
            ->filter()
            ->unique()
            ->sortByDesc(fn (string $label): int => $this->extractVersionNumber($label))
            ->values();

        if ($normalizedLabels->isEmpty()) {
            $normalizedLabels = collect(['v1']);
        }

        $metadata = is_array($project->metadata) ? $project->metadata : [];
        $resolvedActive = $activeVersion !== null
            ? $this->normalizeVersionLabel($activeVersion)
            : $this->normalizeVersionLabel($metadata['active_roadmap_version'] ?? '');

        if (!$normalizedLabels->contains($resolvedActive)) {
            $resolvedActive = $normalizedLabels->first();
        }

        $metadata['roadmap_versions'] = $normalizedLabels->all();
        $metadata['active_roadmap_version'] = $resolvedActive;

        $project->update([
            'metadata' => $metadata,
        ]);
    }

    private function milestonesForVersion(TrsProject $project, string $version)
    {
        $normalizedVersion = $this->normalizeVersionLabel($version);

        if ($normalizedVersion === 'v1') {
            return $project->milestones()->where(function ($query): void {
                $query->where('version', 'v1')->orWhereNull('version')->orWhere('version', '');
            });
        }

        return $project->milestones()->where('version', $normalizedVersion);
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

    private function extractVersionNumber(string $label): int
    {
        if (preg_match('/^v(\d+)$/i', trim($label), $matches) === 1) {
            return max((int) $matches[1], 1);
        }

        return 1;
    }
}
