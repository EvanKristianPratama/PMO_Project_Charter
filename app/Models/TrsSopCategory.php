<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrsSopCategory extends Model
{
    protected $table = 'trs_sop_category';

    protected $fillable = [
        'regulation_id',
        'tipe',
    ];

    public function regulation()
    {
        return $this->belongsTo(MstRegulation::class, 'regulation_id');
    }

    public function procedure()
    {
        return $this->hasMany(MstSop::class, 'category_id');
    }
}
