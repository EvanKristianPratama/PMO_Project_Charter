<?php

namespace Modules\ITOM\Models;

use Illuminate\Database\Eloquent\Model;

class MstGeneralPolicy extends Model
{
    protected $table = 'mst_general_policy';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'regulation_id',
        'number',
        'description',
    ];

    /**
     * Relasi ke MstRegulation
     */
    public function regulation(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(MstRegulation::class, 'regulation_id');
    }
}
