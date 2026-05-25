<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MstInitiative extends Model
{
    use LogsActivity;

    private const APPROVED_ALIASES = ['approved', 'approve', 'aproved'];

    protected $table = 'mst_initiative';

    protected $fillable = [
        'coe_id',
        'tipe_initiative',
        'business_unit',
        'project_id',
        'code',
        'name',
        'description',
        'status',
        'source',
    ];

    protected static function booted(): void
    {
        static::saved(static function (MstInitiative $initiative): void {
            $initiative->syncApprovedProjectToImplementation();
        });
    }

    public function coe(): BelongsTo
    {
        return $this->belongsTo(MstCoe::class, 'coe_id');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(TrsOrganization::class, 'business_unit');
    }

    public function initiativeRelationsRow(): HasMany
    {
        return $this->hasMany(MstInitiativeRelation::class, 'initiative_code_row');
    }

    public function initiativeRelationsColumn(): HasMany
    {
        return $this->hasMany(MstInitiativeRelation::class, 'initiative_code_column');
    }

    public function pcInitiatives(): HasMany
    {
        return $this->hasMany(PcInitiative::class, 'initiative_id');
    }

    public function masterMilestones(): HasMany
    {
        return $this->hasMany(TrsMasterMilestone::class, 'initiative_id');
    }

    /**
     * Status history entries (trs_status_mstinitiative).
     */
    public function statusHistory(): HasMany
    {
        return $this->hasMany(StatusMstInitiative::class, 'initiative_id');
    }

    /**
     * The latest status entry.
     */
    public function latestStatus(): HasOne
    {
        return $this->hasOne(StatusMstInitiative::class, 'initiative_id')
            ->orderByDesc('id');
    }

    public function mappedProjects(): BelongsToMany
    {
        return $this->belongsToMany(TrsProject::class, 'trs_pc_initiative', 'initiative_id', 'pc_id');
    }

    public function sourceData(): BelongsTo
    {
        return $this->belongsTo(DataSource::class, 'source');
    }

    public function mapSc(): HasMany
    {
        return $this->hasMany(TrsMapSc::class, 'initiative_id');
    }

    public function taggings(): HasMany
    {
        return $this->hasMany(InitiativeTagging::class, 'initiative_id');
    }

    public function relationPosition(): HasOne
    {
        return $this->hasOne(TrsInitiativeRelationPosition::class, 'initiative_id');
    }

    public function resolveCanonicalPlanningStatus(): array
    {
        $aliasMap = [
            'draft' => 'drafting',
            'approve' => 'approved',
            'aproved' => 'approved',
        ];
        $statusRank = [
            'baseline' => 5,
            'approved' => 4,
            'review' => 3,
            'propose' => 2,
            'drafting' => 1,
            'draft' => 1,
        ];

        $history = $this->statusHistory;
        if (! $this->relationLoaded('statusHistory')) {
            $history = $this->statusHistory()->get();
        }

        $absoluteLatest = $history->sortByDesc('id')->first();
        $absStatusRaw = strtolower(trim($absoluteLatest?->status ?? ''));
        $absStatus = $aliasMap[$absStatusRaw] ?? $absStatusRaw;

        // Find highest rank (excluding postpone)
        $highestEntry = $history->filter(fn ($s) => strtolower(trim($s->status)) !== 'postpone')
            ->sortByDesc(fn ($s) => [
                $statusRank[$aliasMap[strtolower(trim($s->status))] ?? strtolower(trim($s->status))] ?? 0,
                $s->id,
            ])
            ->first();

        if ($absStatus === 'postpone') {
            return [
                'canonical' => 'postpone',
                'displayStatus' => $absoluteLatest,
            ];
        }

        if ($highestEntry) {
            $hr = strtolower(trim($highestEntry->status));

            return [
                'canonical' => $aliasMap[$hr] ?? $hr,
                'displayStatus' => $highestEntry,
            ];
        }

        $raw = strtolower(trim($this->status ?? 'drafting'));
        $canonical = $aliasMap[$raw] ?? $raw;

        return [
            'canonical' => $canonical,
            'displayStatus' => (object) ['status' => $canonical, 'notes' => ''],
        ];
    }

    public function latestPlanningStatusValue(): ?string
    {
        return $this->resolveCanonicalPlanningStatus()['canonical'];
    }

    public function isApprovedForImplementation(): bool
    {
        $normalizedStatus = strtolower(trim((string) $this->latestPlanningStatusValue()));

        return in_array($normalizedStatus, self::APPROVED_ALIASES, true);
    }

    public function syncApprovedProjectToImplementation(): ?TrsProject
    {
        if (! $this->exists || ! $this->isApprovedForImplementation()) {
            return null;
        }

        $project = $this->findAutoSyncedProject();

        if ($project) {
            $payload = [
                'name' => $this->name,
                'tipe_inisiative' => (string) $this->tipe_initiative,
            ];

            if ($this->isAutoSyncedProject($project)) {
                $payload['status'] = 0;
            }

            $project->update($payload);

            return $project->fresh();
        }

        return TrsProject::query()->create([
            'code' => $this->autoSyncedProjectCode(),
            'name' => $this->name,
            'status' => 0,
            'tipe_inisiative' => (string) $this->tipe_initiative,
        ]);
    }

    private function findAutoSyncedProject(): ?TrsProject
    {
        $project = TrsProject::query()
            ->where('code', $this->autoSyncedProjectCode())
            ->first();

        if ($project) {
            return $project;
        }

        return TrsProject::query()
            ->where('name', $this->name)
            ->where('tipe_inisiative', (string) $this->tipe_initiative)
            ->first();
    }

    private function autoSyncedProjectCode(): string
    {
        return sprintf('AUTO-MI-%d', $this->id);
    }

    private function isAutoSyncedProject(TrsProject $project): bool
    {
        return $project->code === $this->autoSyncedProjectCode();
    }

    public function statusImplementations(): HasMany
    {
        return $this->hasMany(TrsStatusImplementation::class, 'initiative_id');
    }

    public function latestStatusImplementation(): HasOne
    {
        return $this->hasOne(TrsStatusImplementation::class, 'initiative_id')
            ->orderByDesc('id');
    }

    public function mapTechnologies(): HasMany
    {
        return $this->hasMany(TrsMapTechnology::class, 'initiative_id');
    }

    public function itBuildingMapping(): HasOne
    {
        return $this->hasOne(TrsMapItBuilding::class, 'initiative_id');
    }
}
