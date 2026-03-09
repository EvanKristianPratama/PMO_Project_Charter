<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MstInitiative extends Model
{
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

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(
            TrsOrganization::class,
            'trs_bu_collaboration',
            'initiative_id',
            'ogranitiation_id',
        )->withTimestamps();
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
    public function latestStatus(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(StatusMstInitiative::class, 'initiative_id')->latestOfMany();
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

    public function latestPlanningStatusValue(): ?string
    {
        $latestStatus = $this->relationLoaded('latestStatus')
            ? $this->latestStatus
            : $this->latestStatus()->first();

        $status = trim((string) ($latestStatus?->status ?? ''));
        if ($status !== '') {
            return $status;
        }

        $fallbackStatus = trim((string) ($this->status ?? ''));

        return $fallbackStatus !== '' ? $fallbackStatus : null;
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
        $metadata = is_array($project?->metadata) ? $project->metadata : [];
        $metadata['mst_initiative_id'] = (int) $this->id;
        $metadata['auto_synced_from_mst_initiative'] = true;

        if ($project) {
            $payload = [
                'name' => $this->name,
                'tipe_inisiative' => (string) $this->tipe_initiative,
                'metadata' => $metadata,
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
            'metadata' => $metadata,
        ]);
    }

    private function findAutoSyncedProject(): ?TrsProject
    {
        $project = TrsProject::query()
            ->where('code', $this->autoSyncedProjectCode())
            ->orWhere('metadata->mst_initiative_id', $this->id)
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
        $metadata = is_array($project->metadata) ? $project->metadata : [];

        return $project->code === $this->autoSyncedProjectCode()
            || (bool) ($metadata['auto_synced_from_mst_initiative'] ?? false)
            || (int) ($metadata['mst_initiative_id'] ?? 0) === (int) $this->id;
    }
}
