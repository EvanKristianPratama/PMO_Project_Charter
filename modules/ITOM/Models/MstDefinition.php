<?php

namespace Modules\ITOM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MstDefinition extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mst_definition';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'definition',
    ];

    /**
     * Relasi ke MstRegulation via trs_definition_regulation.
     */
    public function regulations(): BelongsToMany
    {
        return $this->belongsToMany(MstRegulation::class, 'trs_definition_regulation', 'definition_id', 'regulation_id')
            ->withTimestamps();
    }

    /**
     * Relasi ke TrsDefinitionRegulation.
     */
    public function definitionRegulations(): HasMany
    {
        return $this->hasMany(TrsDefinitionRegulation::class, 'definition_id');
    }
}
