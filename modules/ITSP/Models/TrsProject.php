<?php

namespace Modules\ITSP\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrsProject extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    /** Kolom teknis metadata tidak perlu dicatat diff-nya */
    protected array $auditExclude = ['metadata'];

    protected $table = 'trs_projects';

    protected $fillable = [
        'code',
        'name',
        'owner_id',
        'owner_name',
        'status',
        'metadata',
        'tipe_inisiative',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'status' => 'integer',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function charter(): HasOne
    {
        return $this->projectCharter();
    }

    public function projectCharter(): HasOne
    {
        return $this->hasOne(TrsProjectCharter::class, 'project_id')->latestOfMany('id');
    }

    public function versionAnalysis(): HasMany
    {
        return $this->hasMany(TrsPcVersionAnalysis::class, 'project_id')->orderBy('id', 'desc');
    }

    public function charters(): HasMany
    {
        return $this->projectCharters();
    }

    public function projectCharters(): HasMany
    {
        return $this->hasMany(TrsProjectCharter::class, 'project_id');
    }

    public function statusRef(): BelongsTo
    {
        return $this->belongsTo(InitiativeStatus::class, 'status');
    }

    public function reviewPcStatusImplementations(): HasMany
    {
        return $this->hasMany(TrsReviewPCStatusImplementation::class, 'project_id')
            ->orderBy('id', 'desc');
    }

    public function pcStatusImplementations(): HasMany
    {
        return $this->hasMany(TrsPcStatusImplementation::class, 'project_id')
            ->orderBy('year', 'asc')
            ->orderByRaw(
                "CASE LOWER(TRIM(COALESCE(trs_pc_status_implementation.month, '')))
                    WHEN 'januari' THEN 1
                    WHEN 'februari' THEN 2
                    WHEN 'maret' THEN 3
                    WHEN 'april' THEN 4
                    WHEN 'mei' THEN 5
                    WHEN 'juni' THEN 6
                    WHEN 'juli' THEN 7
                    WHEN 'agustus' THEN 8
                    WHEN 'september' THEN 9
                    WHEN 'oktober' THEN 10
                    WHEN 'november' THEN 11
                    WHEN 'desember' THEN 12
                    ELSE 0
                END"
            )
            ->orderBy('id', 'asc');
    }

    public function projectStatusHistories(): HasManyThrough
    {
        return $this->hasManyThrough(
            ProjectStatusHistory::class,
            TrsProjectCharter::class,
            'project_id',
            'project_charter_id',
            'id',
            'id'
        )
            ->select('trs_project_status_history.*')
            ->orderByDesc('trs_project_status_history.version')
            ->orderByDesc('trs_project_status_history.tanggal')
            ->orderByDesc('trs_project_status_history.id');
    }

    public function latestPcStatusImplementation(): HasOne
    {
        return $this->hasOne(TrsPcStatusImplementation::class, 'project_id')
            ->orderBy('year', 'desc')
            ->orderByRaw(
                "CASE LOWER(TRIM(COALESCE(trs_pc_status_implementation.month, '')))
                    WHEN 'januari' THEN 1
                    WHEN 'februari' THEN 2
                    WHEN 'maret' THEN 3
                    WHEN 'april' THEN 4
                    WHEN 'mei' THEN 5
                    WHEN 'juni' THEN 6
                    WHEN 'juli' THEN 7
                    WHEN 'agustus' THEN 8
                    WHEN 'september' THEN 9
                    WHEN 'oktober' THEN 10
                    WHEN 'november' THEN 11
                    WHEN 'desember' THEN 12
                    ELSE 0
                END DESC"
            )
            ->orderByDesc('id');
    }

    public function mappedInitiatives(): BelongsToMany
    {
        return $this->belongsToMany(MstInitiative::class, 'trs_pc_initiative', 'pc_id', 'initiative_id');
    }

    public function mapPicProject(): HasOne
    {
        return $this->hasOne(TrsMapPicProject::class, 'project_id');
    }

    public function mapCrossFunctions(): HasMany
    {
        return $this->hasMany(TrsMapCrossFunction::class, 'pc_id');
    }
}
