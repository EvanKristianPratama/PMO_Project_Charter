<?php

namespace App\Http\Controllers\ITInitiative;

use App\Http\Controllers\Controller;
use App\Http\Requests\ITInitiative\MilestoneStoreRequest;
use App\Models\Milestone;
use App\Models\ProjectCharter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class MilestoneController extends Controller
{
    public function store(MilestoneStoreRequest $request, ProjectCharter $project): RedirectResponse
    {
        $charter = $project;
        $payload = $request->validated();
        $targetVersion = $this->normalizeVersionLabel($charter->version_label);
        $nextOrder = (int) ($this->milestonesForVersion($charter, $targetVersion)
            ->max('order') ?? 0) + 1;

        Milestone::query()->create([
            'project_id' => (int) $charter->project_id,
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

        $this->persistRoadmapVersionMeta($charter, $this->charterVersionLabels($charter, false));

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
            'project_id' => (int) $charter->project_id,
            'pc_id' => (int) $charter->id,
            'version' => $targetVersion,
            'title' => $payload['title'],
            'output' => $payload['output'] ?? null,
            'type' => $payload['type'],
            'milestone_type' => Milestone::normalizeRoadmapType($payload['milestone_type'] ?? null),
            'start_date' => $payload['start_date'] ?? null,
            'end_date' => $payload['end_date'] ?? null,
        ]);

        $this->persistRoadmapVersionMeta($charter, $this->charterVersionLabels($charter, false));

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

    private function resolveVersionForCharter(ProjectCharter $charter, mixed $requestedVersion): string
    {
        return $this->normalizeVersionLabel($charter->version_label);
    }

    private function charterVersionLabels(ProjectCharter $charter, bool $withDefault = true): Collection
    {
        return collect([$this->normalizeVersionLabel($charter->version_label)]);
    }

    private function milestonesQueryForCharter(ProjectCharter $charter): Builder
    {
        return Milestone::query()->where('pc_id', $charter->id);
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

    private function normalizeLegacyVersionDataForCharter(ProjectCharter $charter): void
    {
        $this->milestonesQueryForCharter($charter)
            ->where(function ($query): void {
                $query->whereNull('version')->orWhere('version', '');
            })
            ->update(['version' => 'v1']);
    }

    private function roadmapVersionLabelsFromMeta(ProjectCharter $charter): Collection
    {
        $metadata = is_array($charter->metadata) ? $charter->metadata : [];
        $labels = $metadata['roadmap_versions'] ?? [];

        if (is_array($labels) && !empty($labels)) {
            return collect($labels);
        }

        // Compatibility fallback for older data stored in project metadata.
        $projectMetadata = is_array($charter->project?->metadata) ? $charter->project->metadata : [];
        $projectLabels = $projectMetadata['roadmap_versions'] ?? [];

        if (!is_array($projectLabels)) {
            return collect();
        }

        return collect($projectLabels);
    }

    private function persistRoadmapVersionMeta(ProjectCharter $charter, Collection $labels, ?string $activeVersion = null): void
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

        $metadata = is_array($charter->metadata) ? $charter->metadata : [];
        $resolvedActive = $activeVersion !== null
            ? $this->normalizeVersionLabel($activeVersion)
            : $this->normalizeVersionLabel($metadata['active_roadmap_version'] ?? '');

        if (!$normalizedLabels->contains($resolvedActive)) {
            $resolvedActive = $normalizedLabels->first();
        }

        $metadata['roadmap_versions'] = $normalizedLabels->all();
        $metadata['active_roadmap_version'] = $resolvedActive;

        $charter->update([
            'metadata' => $metadata,
        ]);
    }

    private function milestonesForVersion(ProjectCharter $charter, string $version): Builder
    {
        $normalizedVersion = $this->normalizeVersionLabel($version);
        $query = $this->milestonesQueryForCharter($charter);

        if ($normalizedVersion === 'v1') {
            return $query->where(function ($innerQuery): void {
                $innerQuery->where('version', 'v1')->orWhereNull('version')->orWhere('version', '');
            });
        }

        return $query->where('version', $normalizedVersion);
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
