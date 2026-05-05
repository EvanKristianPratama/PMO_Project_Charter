<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class MstMisiBumn extends Model
{
    protected $table = 'mst_misi_bumn';
    protected $fillable = ['code', 'name'];

    public function prioritasStrategy(): HasMany
    {
        return $this->hasMany(MstBusinessStrategy::class, 'misi_id');
    }
}
