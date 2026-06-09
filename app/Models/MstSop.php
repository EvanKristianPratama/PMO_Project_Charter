<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MstSop extends Model
{
    protected $table = 'trs_sop';

    protected $fillable = [
        'category_id',
        'tipe',
        'description'
    ];

    public function regulation()
    {
        return $this->hasOneThrough(
            MstRegulation::class,
            TrsSopCategory::class,
            'id',            // Foreign key on trs_sop_category table
            'id',            // Foreign key on mst_regulation table
            'category_id',   // Local key on mst_sop table
            'regulation_id'  // Local key on trs_sop_category table
        );
    }

    public function mapActorSops()
    {
        return $this->hasMany(TrsMapActorSop::class, 'sop_id');
    }

    public function category()
    {
        return $this->belongsTo(TrsSopCategory::class, 'category_id');
    }
}
